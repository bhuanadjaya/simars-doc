<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DocumentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private Collection $documents) {}

    public function collection(): Collection
    {
        return $this->documents;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Dokumen',
            'Judul',
            'Jenis Dokumen',
            'Unit',
            'Sumber',
            'Tanggal Berlaku',
            'Masa Berlaku (s/d)',
            'Status',
            'Alasan Obsolete',
        ];
    }

    public function map($doc): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $doc->number,
            $doc->title,
            $doc->documentType?->name ?? '—',
            $doc->ownerUnit?->name ?? '—',
            ucfirst($doc->source),
            $doc->effective_date?->format('d/m/Y') ?? '—',
            $doc->expired_at?->format('d/m/Y') ?? '—',
            ucfirst($doc->status),
            $doc->obsolete_reason ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
