<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MakeExportCommandTest extends TestCase
{
    public function test_make_export_command_creates_file()
    {
        $name = 'Test';
        $path = base_path("app/Exports/{$name}ExportService.php");
        if (file_exists($path)) {
            unlink($path);
        }
        Artisan::call('make:export', ['name' => $name]);
        $this->assertFileExists($path);
        unlink($path);
    }
}
