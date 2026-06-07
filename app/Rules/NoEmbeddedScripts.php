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

        // PDF JavaScript indicators
        $patterns = ['/JS', '/JavaScript', '/OpenAction', '/Launch', '/AA'];

        foreach ($patterns as $pattern) {
            if (str_contains($content, $pattern)) {
                return true;
            }
        }

        return false;
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
