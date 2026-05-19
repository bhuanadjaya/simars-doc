<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExternalRegulationRequest;
use App\Http\Requests\UpdateExternalRegulationRequest;
use App\Jobs\SendDocumentNotifications;
use App\Models\ExternalRegulation;
use App\Models\Notification;
use App\Models\Unit;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ExternalRegulationController extends Controller
{
    public function __construct(private ActivityLogService $activityLog) {}

    public function index(Request $request): View
    {
        $query = ExternalRegulation::latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('regulation_number', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%")
                    ->orWhere('issuing_agency', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $regulations = $query->paginate(20)->withQueryString();

        return view('admin.external-regulations.index', compact('regulations'));
    }

    public function create(): View
    {
        $units      = Unit::where('is_active', true)->orderBy('name')->get();
        $categories = ExternalRegulation::$categoryLabels;

        return view('admin.external-regulations.create', compact('units', 'categories'));
    }

    public function store(StoreExternalRegulationRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user      = auth()->user();
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request, $user) {
            $year    = now()->year;
            $pdfFile = $request->file('pdf_file');
            $path    = $pdfFile->store("regulations/{$year}", 'local');

            $regulation = ExternalRegulation::create([
                'regulation_number' => $validated['regulation_number'],
                'title'             => $validated['title'],
                'issuing_agency'    => $validated['issuing_agency'],
                'category'          => $validated['category'],
                'issued_date'       => $validated['issued_date'],
                'effective_date'    => $validated['effective_date'],
                'file_path'         => $path,
                'status'            => 'active',
                'affected_unit_ids' => $validated['affected_unit_ids'] ?? null,
                'uploaded_by'       => $user->id,
            ]);

            // Send notifications to users in affected units via Queue
            $unitIds = $validated['affected_unit_ids'] ?? [];
            if (! empty($unitIds)) {
                $recipients = User::whereIn('unit_id', $unitIds)
                    ->where('is_active', true)
                    ->whereNull('deleted_at')
                    ->get();

                if ($recipients->isNotEmpty()) {
                    // Bulk insert notifications directly for regulations (no Document FK)
                    $now   = now();
                    $rows  = $recipients->map(fn ($u) => [
                        'id'         => \Illuminate\Support\Str::uuid()->toString(),
                        'user_id'    => $u->id,
                        'document_id'=> null,
                        'title'      => 'Regulasi Baru: ' . $regulation->title,
                        'message'    => 'Regulasi eksternal "' . $regulation->regulation_number . ' — ' . $regulation->title . '" telah diunggah.',
                        'type'       => 'new_regulation',
                        'is_read'    => false,
                        'read_at'    => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all();

                    foreach (array_chunk($rows, 100) as $chunk) {
                        Notification::insert($chunk);
                    }
                }
            }

            $this->activityLog->log($user, 'upload_regulation');
        });

        return redirect()->route('admin.external-regulations.index')
            ->with('success', 'Regulasi eksternal berhasil diunggah.');
    }

    public function show(ExternalRegulation $externalRegulation): View
    {
        $externalRegulation->load('uploader');
        $affectedUnits = collect();

        if (! empty($externalRegulation->affected_unit_ids)) {
            $affectedUnits = Unit::whereIn('id', $externalRegulation->affected_unit_ids)->get();
        }

        $categories = ExternalRegulation::$categoryLabels;

        return view('admin.external-regulations.show', compact('externalRegulation', 'affectedUnits', 'categories'));
    }

    public function edit(ExternalRegulation $externalRegulation): View
    {
        $units      = Unit::where('is_active', true)->orderBy('name')->get();
        $categories = ExternalRegulation::$categoryLabels;

        return view('admin.external-regulations.edit', compact('externalRegulation', 'units', 'categories'));
    }

    public function update(UpdateExternalRegulationRequest $request, ExternalRegulation $externalRegulation): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user      = auth()->user();
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request, $user, $externalRegulation) {
            $oldPath = null;

            if ($request->hasFile('pdf_file')) {
                $oldPath = $externalRegulation->file_path;
                $year    = now()->year;
                $path    = $request->file('pdf_file')->store("regulations/{$year}", 'local');
                $validated['file_path'] = $path;
            }

            unset($validated['pdf_file']);
            $validated['affected_unit_ids'] = $validated['affected_unit_ids'] ?? null;

            $externalRegulation->update($validated);

            if ($oldPath) {
                Storage::disk('local')->delete($oldPath);
            }

            $this->activityLog->log($user, 'update_regulation');
        });

        return redirect()->route('admin.external-regulations.show', $externalRegulation)
            ->with('success', 'Regulasi eksternal berhasil diperbarui.');
    }

    public function destroy(ExternalRegulation $externalRegulation): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $filePath = $externalRegulation->file_path;
        $externalRegulation->delete();
        Storage::disk('local')->delete($filePath);

        $this->activityLog->log($user, 'delete_regulation');

        return redirect()->route('admin.external-regulations.index')
            ->with('success', 'Regulasi eksternal berhasil dihapus.');
    }

    public function download(ExternalRegulation $externalRegulation): Response|RedirectResponse
    {
        abort_unless(Storage::disk('local')->exists($externalRegulation->file_path), 404);

        $filename = $externalRegulation->regulation_number . '.pdf';

        return Storage::disk('local')->download($externalRegulation->file_path, $filename);
    }

    public function stream(ExternalRegulation $externalRegulation): Response
    {
        abort_unless(Storage::disk('local')->exists($externalRegulation->file_path), 404);

        $contents = Storage::disk('local')->get($externalRegulation->file_path);

        return response($contents, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $externalRegulation->regulation_number . '.pdf"',
        ]);
    }
}
