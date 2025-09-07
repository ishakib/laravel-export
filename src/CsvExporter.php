<?php

namespace Vendor\Export;

use Illuminate\Support\Facades\Storage;

class CsvExporter implements ExporterInterface
{
    use ExportPathHelper;

    public function export(iterable $data, array $columns, string $path, string $format): string
    {
        if ($format !== 'csv') {
            throw new \InvalidArgumentException('CsvExporter only supports format: csv');
        }
        $csv = '';
        $csv .= implode(',', $columns) . "\n";
        foreach ($data as $row) {
            $csv .= implode(',', array_map(fn($col) => str_replace(["\n", ","], [' ', ' '], $row[$col] ?? ''), $columns)) . "\n";
        }
        $exportPath = 'exports/' . basename($path);
        Storage::disk(config('filesystems.default', 'local'))->put($exportPath, $csv);
        return $exportPath;
    }
}
