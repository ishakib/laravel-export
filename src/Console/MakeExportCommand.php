<?php

namespace Vendor\Export\Console;

use Illuminate\Console\Command;

class MakeExportCommand extends Command
{
    protected $signature = 'make:export {name}';
    protected $description = 'Create a new export service class';

    public function handle()
    {
        $name = $this->argument('name');
        // Use PHP functions for path resolution
        $basePath = getcwd();
        $exportDir = $basePath . '/app/Exports';
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }
        $path = $exportDir . "/{$name}ExportService.php";
        if (file_exists($path)) {
            echo "Export service already exists!\n";
            return 1;
        }
        $stub = file_get_contents(__DIR__.'/stubs/export-service.stub');
        $stub = str_replace('DummyExportService', "{$name}ExportService", $stub);
        // Remove stub wrapping if present
        if (strpos($stub, 'return') === 0) {
            $stub = preg_replace("/^return <<<'STUB'\\n|STUB;$/m", '', $stub);
        }
        file_put_contents($path, $stub);
        echo "Export service created: {$path}\n";
        return 0;
    }
}
