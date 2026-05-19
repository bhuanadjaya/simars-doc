<?php

namespace App\Services;

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
