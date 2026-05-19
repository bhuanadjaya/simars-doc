<?php

namespace App\Services;

use App\Jobs\SendDocumentNotifications;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DocumentService
{
    public function store(array $data, UploadedFile $pdfFile, ?UploadedFile $docxFile, User $uploader): Document
    {
        $uploadedPaths = [];

        try {
            return DB::transaction(function () use ($data, $pdfFile, $docxFile, $uploader, &$uploadedPaths) {
                $document = Document::create(array_merge($data, [
                    'uploaded_by' => $uploader->id,
                    'status'      => 'draft',
                ]));

                $document->load('ownerUnit');

                // PDF (required)
                $pdfPath = $this->saveFile($pdfFile, $document);
                $uploadedPaths[] = $pdfPath;

                DocumentFile::create([
                    'document_id'       => $document->id,
                    'file_type'         => 'pdf',
                    'original_filename' => $pdfFile->getClientOriginalName(),
                    'file_path'         => $pdfPath,
                    'file_size'         => $pdfFile->getSize(),
                    'mime_type'         => $pdfFile->getMimeType(),
                    'uploaded_by'       => $uploader->id,
                ]);

                // DOCX (optional)
                if ($docxFile) {
                    $docxPath = $this->saveFile($docxFile, $document);
                    $uploadedPaths[] = $docxPath;

                    DocumentFile::create([
                        'document_id'       => $document->id,
                        'file_type'         => 'docx',
                        'original_filename' => $docxFile->getClientOriginalName(),
                        'file_path'         => $docxPath,
                        'file_size'         => $docxFile->getSize(),
                        'mime_type'         => $docxFile->getMimeType(),
                        'uploaded_by'       => $uploader->id,
                    ]);
                }

                return $document;
            });
        } catch (Throwable $e) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('local')->delete($path);
            }
            throw $e;
        }
    }

    public function publish(Document $document): void
    {
        if ($document->status !== 'draft') {
            throw new \RuntimeException('Only draft documents can be published.');
        }

        $hasPdf = $document->files()->where('file_type', 'pdf')->exists();
        if (! $hasPdf) {
            throw new \RuntimeException('Please upload a PDF file before publishing.');
        }

        DB::transaction(function () use ($document) {
            $document->update([
                'status'       => 'active',
                'published_at' => today(),
            ]);

            // Load unit users for notification dispatch
            $recipients = \App\Models\User::where('unit_id', $document->owner_unit_id)
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->get();

            if ($recipients->isNotEmpty()) {
                SendDocumentNotifications::dispatch(
                    $document,
                    $recipients,
                    'new_document',
                    'Dokumen Baru: ' . $document->title,
                    'Dokumen "' . $document->number . ' — ' . $document->title . '" telah dipublikasikan.',
                );
            }
        });
    }

    public function setObsolete(Document $document, User $actor, string $reason, ?string $replacedById): void
    {
        if ($document->status !== 'active') {
            throw new \RuntimeException('Only active documents can be set to obsolete.');
        }

        DB::transaction(function () use ($document, $actor, $reason, $replacedById) {
            $document->update([
                'status'          => 'obsolete',
                'obsolete_date'   => today(),
                'obsolete_reason' => $reason,
                'obsoleted_by'    => $actor->id,
                'replaced_by_id'  => $replacedById ?: null,
            ]);

            // Notify users who previously downloaded this document
            $downloaderIds = \App\Models\ActivityLog::where('document_id', $document->id)
                ->where('action', 'download_document')
                ->distinct('user_id')
                ->pluck('user_id');

            if ($downloaderIds->isNotEmpty()) {
                $recipients = User::whereIn('id', $downloaderIds)
                    ->where('is_active', true)
                    ->whereNull('deleted_at')
                    ->get();

                if ($recipients->isNotEmpty()) {
                    SendDocumentNotifications::dispatch(
                        $document,
                        $recipients,
                        'document_obsolete',
                        'Dokumen Obsolet: ' . $document->title,
                        'Dokumen "' . $document->number . ' — ' . $document->title . '" telah dinyatakan obsolet.',
                    );
                }
            }
        });
    }

    public function update(Document $document, array $data, ?UploadedFile $pdfFile, ?UploadedFile $docxFile, User $uploader): Document
    {
        $newPaths = [];
        $oldPaths = [];

        try {
            $result = DB::transaction(function () use ($document, $data, $pdfFile, $docxFile, $uploader, &$newPaths, &$oldPaths) {
                $document->update($data);
                $document->load('ownerUnit');

                if ($pdfFile) {
                    $existing = $document->files()->where('file_type', 'pdf')->first();
                    if ($existing) {
                        $oldPaths[] = $existing->file_path;
                        $existing->delete();
                    }

                    $path = $this->saveFile($pdfFile, $document);
                    $newPaths[] = $path;

                    DocumentFile::create([
                        'document_id'       => $document->id,
                        'file_type'         => 'pdf',
                        'original_filename' => $pdfFile->getClientOriginalName(),
                        'file_path'         => $path,
                        'file_size'         => $pdfFile->getSize(),
                        'mime_type'         => $pdfFile->getMimeType(),
                        'uploaded_by'       => $uploader->id,
                    ]);
                }

                if ($docxFile) {
                    $existing = $document->files()->where('file_type', 'docx')->first();
                    if ($existing) {
                        $oldPaths[] = $existing->file_path;
                        $existing->delete();
                    }

                    $path = $this->saveFile($docxFile, $document);
                    $newPaths[] = $path;

                    DocumentFile::create([
                        'document_id'       => $document->id,
                        'file_type'         => 'docx',
                        'original_filename' => $docxFile->getClientOriginalName(),
                        'file_path'         => $path,
                        'file_size'         => $docxFile->getSize(),
                        'mime_type'         => $docxFile->getMimeType(),
                        'uploaded_by'       => $uploader->id,
                    ]);
                }

                return $document->fresh(['documentType', 'ownerUnit', 'uploader', 'files']);
            });

            // Delete old physical files only after transaction commits
            foreach ($oldPaths as $path) {
                Storage::disk('local')->delete($path);
            }

            return $result;
        } catch (Throwable $e) {
            foreach ($newPaths as $path) {
                Storage::disk('local')->delete($path);
            }
            throw $e;
        }
    }

    private function saveFile(UploadedFile $file, Document $document): string
    {
        $unitCode = $document->ownerUnit->code;
        $year     = now()->year;
        $dir      = "documents/{$unitCode}/{$year}/{$document->id}";

        return $file->store($dir, 'local');
    }
}
