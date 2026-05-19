<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Induk Dokumen — SIMARS-DOC</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #111; padding: 20px; }
        h1 { font-size: 16px; font-weight: bold; margin-bottom: 2px; }
        .subtitle { font-size: 11px; color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; text-align: left; padding: 6px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; border: 1px solid #e5e7eb; }
        td { padding: 5px 8px; border: 1px solid #e5e7eb; vertical-align: top; }
        tr:nth-child(even) td { background: #f9fafb; }
        .footer { margin-top: 20px; font-size: 10px; color: #666; text-align: right; }
        @media print { body { padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px;">
        <button onclick="window.print()" style="background:#1d4ed8; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:13px;">
            Cetak / Simpan PDF
        </button>
    </div>

    <h1>Daftar Induk Dokumen</h1>
    <p class="subtitle">SIMARS-DOC — Dicetak: {{ now()->format('d/m/Y H:i') }} — Total: {{ $documents->count() }} dokumen</p>

    <table>
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th>Nomor Dokumen</th>
                <th>Judul</th>
                <th>Jenis</th>
                <th>Unit Pemilik</th>
                <th>Tgl Berlaku</th>
                <th>Tgl Publikasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($documents as $i => $doc)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family: monospace; font-size: 10px;">{{ $doc->number }}</td>
                    <td>{{ $doc->title }}</td>
                    <td>{{ $doc->documentType?->code }}</td>
                    <td>{{ $doc->ownerUnit?->name }}</td>
                    <td>{{ $doc->effective_date?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $doc->published_at?->format('d/m/Y') ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">SIMARS-DOC · Daftar Induk Dokumen · {{ now()->format('d/m/Y') }}</p>
</body>
</html>
