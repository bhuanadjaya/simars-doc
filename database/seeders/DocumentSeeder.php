<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $units         = DB::table('units')->where('is_active', true)->get();
        $documentTypes = DB::table('document_types')->where('is_active', true)->get();
        $uploader      = DB::table('users')->first();

        if (! $uploader || $units->isEmpty() || $documentTypes->isEmpty()) {
            $this->command->warn('Seed dibatalkan: pastikan users, units, dan document_types sudah ada.');
            return;
        }

        // Write one shared dummy PDF (minimal valid PDF)
        $dummyPdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
            . "2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n"
            . "3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R>>endobj\n"
            . "xref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n"
            . "0000000058 00000 n\n0000000115 00000 n\n"
            . "trailer<</Size 4/Root 1 0 R>>\nstartxref\n190\n%%EOF";

        $dummyPath = 'documents/seed/dummy.pdf';
        Storage::disk('local')->put($dummyPath, $dummyPdfContent);

        $titles = [
            'Standar Prosedur Operasional Triase IGD',
            'Prosedur Penanganan Pasien Kritis',
            'Pedoman Penggunaan Obat-obatan Emergensi',
            'SPO Resusitasi Jantung Paru',
            'Kebijakan Keselamatan Pasien',
            'Standar Pelayanan Minimal Rawat Inap',
            'Prosedur Pencegahan dan Pengendalian Infeksi',
            'Pedoman Asesmen Pasien Rawat Jalan',
            'SPO Pengambilan Sampel Darah',
            'Prosedur Pemeriksaan Laboratorium STAT',
            'Kebijakan Dispensing Obat Narkotika',
            'Standar Penyimpanan Obat High Alert',
            'SPO Perawatan Luka Post Operasi',
            'Pedoman Transfusi Darah',
            'Prosedur Identifikasi Pasien',
            'Kebijakan Informed Consent',
            'SPO Pengelolaan Limbah Medis',
            'Standar Kebersihan Tangan (Hand Hygiene)',
            'Prosedur Sterilisasi Alat Medis',
            'Pedoman Pencatatan Rekam Medis',
            'SPO Penanganan Pasien Jatuh',
            'Prosedur Komunikasi SBAR',
            'Kebijakan Hak dan Kewajiban Pasien',
            'Standar Pembuangan Benda Tajam',
            'SPO Manajemen Nyeri',
            'Prosedur Pemasangan Infus',
            'Pedoman Pemberian Nutrisi Parenteral',
            'Kebijakan Penolakan Tindakan Medis',
            'SPO Pemantauan Tanda Vital',
            'Standar Pelayanan Apoteker Klinik',
        ];

        $statuses = ['draft', 'draft', 'active', 'active', 'active', 'active', 'obsolete'];
        $sources  = ['internal', 'internal', 'internal', 'external'];

        $now = now();

        foreach ($titles as $index => $title) {
            $unit     = $units->random();
            $type     = $documentTypes->random();
            $status   = $statuses[$index % count($statuses)];
            $source   = $sources[$index % count($sources)];
            $docId    = Str::uuid()->toString();
            $year     = rand(2022, 2025);

            $docData = [
                'id'               => $docId,
                'number'           => strtoupper($type->code) . '/RS/' . str_pad($index + 1, 3, '0', STR_PAD_LEFT) . '/' . $year,
                'title'            => $title,
                'document_type_id' => $type->id,
                'owner_unit_id'    => $unit->id,
                'uploaded_by'      => $uploader->id,
                'source'           => $source,
                'revision_number'  => rand(0, 3),
                'description'      => 'Dokumen ini merupakan ' . $type->name . ' mengenai ' . $title . ' yang berlaku di lingkungan rumah sakit.',
                'tags'             => implode(', ', array_slice(['keselamatan', 'medis', 'prosedur', 'klinis', 'pasien', 'farmasi', 'laboratorium'], 0, rand(2, 4))),
                'status'           => $status,
                'effective_date'   => now()->subMonths(rand(1, 24))->format('Y-m-d'),
                'published_at'     => in_array($status, ['active', 'obsolete']) ? now()->subMonths(rand(1, 20))->format('Y-m-d') : null,
                'obsolete_date'    => $status === 'obsolete' ? now()->subMonths(rand(0, 6))->format('Y-m-d') : null,
                'obsolete_reason'  => $status === 'obsolete' ? 'Digantikan dengan versi yang telah diperbarui.' : null,
                'obsoleted_by'     => $status === 'obsolete' ? $uploader->id : null,
                'replaced_by_id'   => null,
                'parent_document_id' => null,
                'deleted_at'       => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];

            DB::table('documents')->insert($docData);

            // Only create PDF file record for non-draft documents (and half of drafts)
            if ($status !== 'draft' || $index % 2 === 0) {
                DB::table('document_files')->insert([
                    'id'                => Str::uuid()->toString(),
                    'document_id'       => $docId,
                    'file_type'         => 'pdf',
                    'original_filename' => Str::slug($title) . '.pdf',
                    'file_path'         => $dummyPath,
                    'file_size'         => strlen($dummyPdfContent),
                    'mime_type'         => 'application/pdf',
                    'uploaded_by'       => $uploader->id,
                ]);
            }
        }

        $this->command->info('30 dokumen berhasil dibuat.');
    }
}
