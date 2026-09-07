<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class NoEmbeddedScripts implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());

        if ($extension === 'pdf' && $this->pdfContainsScript($value)) {
            $fail('File PDF mengandung script atau aksi berbahaya dan tidak dapat diunggah.');
        }

        if (in_array($extension, ['docx', 'doc']) && $this->docxContainsMacro($value)) {
            $fail('File DOCX mengandung macro dan tidak dapat diunggah.');
        }
    }

    private function pdfContainsScript(UploadedFile $file): bool
    {
        $content = file_get_contents($file->getRealPath());
        if ($content === false) {
            return false;
        }

        return $this->hasDangerousName($this->pdfStructuralContent($content));
    }

    /**
     * Ambil bagian struktural PDF: seluruh isi di luar stream, ditambah isi
     * stream yang berhasil di-inflate (object stream berisi dictionary asli).
     * Data stream mentah dibuang karena byte acak di dalamnya sering
     * memunculkan pola seperti "/AA" secara kebetulan pada file hasil scan.
     */
    private function pdfStructuralContent(string $content): string
    {
        $structural = '';
        $offset     = 0;

        while (($start = strpos($content, 'stream', $offset)) !== false) {
            $structural .= substr($content, $offset, $start - $offset);

            $end = strpos($content, 'endstream', $start);
            if ($end === false) {
                return $structural;
            }

            $raw      = ltrim(substr($content, $start + 6, $end - $start - 6), "\r\n");
            $inflated = @gzuncompress($raw);
            if ($inflated !== false) {
                $structural .= "\n" . $inflated . "\n";
            }

            $offset = $end + 9;
        }

        return $structural . substr($content, $offset);
    }

    /**
     * Hanya action yang benar-benar dapat dieksekusi yang diperiksa.
     *
     * /OpenAction dan /AA sengaja tidak dipakai sebagai penanda: keduanya hanya
     * wadah, dan umum berisi destinasi halaman biasa seperti
     * "/OpenAction[7 0 R/FitH null]" yang dipasang TCPDF di setiap dokumen.
     * Bila wadah itu memang berisi script, payload-nya tetap tertangkap lewat
     * /JS, /JavaScript, atau /Launch — termasuk saat diacu tidak langsung,
     * karena object stream ikut di-inflate lebih dulu.
     *
     * Nama PDF berakhir pada delimiter atau whitespace, sehingga nama seperti
     * /JavaScriptXyz atau subset font /AAAAAC+DejaVuSans tidak ikut cocok.
     */
    private function hasDangerousName(string $content): bool
    {
        $names = ['JS', 'JavaScript', 'Launch'];

        return preg_match(
            '~/(' . implode('|', $names) . ')(?=[\s/<>\[\]()%]|$)~',
            $content
        ) === 1;
    }

    private function docxContainsMacro(UploadedFile $file): bool
    {
        if (! class_exists('ZipArchive')) {
            return false;
        }

        $zip = new \ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            return false;
        }

        // Macro disimpan di vbaProject.bin dalam ZIP DOCX
        $hasMacro = $zip->locateName('word/vbaProject.bin') !== false;
        $zip->close();

        return $hasMacro;
    }
}
