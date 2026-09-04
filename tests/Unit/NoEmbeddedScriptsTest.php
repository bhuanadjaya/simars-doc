<?php

namespace Tests\Unit;

use App\Rules\NoEmbeddedScripts;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;

class NoEmbeddedScriptsTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . '/no-embedded-scripts-' . uniqid();
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/*') as $file) {
            unlink($file);
        }
        rmdir($this->dir);
        parent::tearDown();
    }

    private function assertPdf(string $bytes, bool $shouldFail, string $message): void
    {
        $path = $this->dir . '/' . uniqid() . '.pdf';
        file_put_contents($path, $bytes);

        $failed = false;
        (new NoEmbeddedScripts())->validate(
            'pdf_file',
            new UploadedFile($path, basename($path), 'application/pdf', null, true),
            function () use (&$failed) { $failed = true; }
        );

        $this->assertSame($shouldFail, $failed, $message);
    }

    public function test_it_blocks_javascript_open_action(): void
    {
        $this->assertPdf(
            "%PDF-1.4\n1 0 obj << /Type /Catalog /OpenAction 4 0 R >> endobj\n"
                . "4 0 obj << /S /JavaScript /JS (app.alert('x')) >> endobj\n%%EOF",
            true,
            'PDF dengan /OpenAction + /JS harus ditolak'
        );
    }

    public function test_it_blocks_launch_action(): void
    {
        $this->assertPdf(
            "%PDF-1.4\n1 0 obj << /OpenAction << /S /Launch /F (calc.exe) >> >> endobj\n%%EOF",
            true,
            'PDF dengan /Launch harus ditolak'
        );
    }

    public function test_it_blocks_additional_action(): void
    {
        $this->assertPdf(
            "%PDF-1.4\n3 0 obj << /Type /Page /AA << /O 4 0 R >> >> endobj\n%%EOF",
            true,
            'PDF dengan key /AA harus ditolak'
        );
    }

    public function test_it_blocks_script_hidden_in_compressed_object_stream(): void
    {
        $inner = "<< /Type /Action /S /JavaScript /JS (app.alert('hidden')) >>";
        $comp  = gzcompress($inner);

        $this->assertPdf(
            "%PDF-1.5\n1 0 obj << /Type /ObjStm /Filter /FlateDecode /Length "
                . strlen($comp) . " >>\nstream\n" . $comp . "\nendstream\nendobj\n%%EOF",
            true,
            'Script di dalam stream terkompresi harus tetap terdeteksi'
        );
    }

    public function test_it_allows_binary_stream_that_merely_looks_like_a_key(): void
    {
        // Byte acak "/AA" di dalam data stream (umum pada PDF hasil scan)
        // bukan key dictionary dan tidak boleh menolak file.
        $noise = "\x9d\xc3\x00/AA#\xbfz\x09\xe7\xa5\x19\x1e\x94\x00";

        $this->assertPdf(
            "%PDF-1.4\n1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n"
                . "5 0 obj << /Length " . strlen($noise) . " >>\nstream\n" . $noise . "\nendstream\nendobj\n%%EOF",
            false,
            'Byte acak di dalam stream tidak boleh dianggap script'
        );
    }

    public function test_it_allows_name_that_only_starts_with_a_blocked_name(): void
    {
        $this->assertPdf(
            "%PDF-1.4\n1 0 obj << /Type /Catalog /AAPLColorSpace /DeviceRGB >> endobj\n%%EOF",
            false,
            '/AAPLColorSpace bukan key /AA dan harus lolos'
        );
    }

    public function test_it_allows_a_clean_pdf(): void
    {
        $this->assertPdf(
            "%PDF-1.4\n1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n"
                . "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n%%EOF",
            false,
            'PDF bersih harus lolos'
        );
    }
}
