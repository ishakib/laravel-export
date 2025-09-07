<?php

namespace Vendor\Export;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfExporter implements ExporterInterface
{
    use ExportPathHelper;

    public function export(iterable $data, array $columns, string $path, string $format): string
    {
        if ($format !== 'pdf') {
            throw new \InvalidArgumentException('PdfExporter only supports format: pdf');
        }
        $rows = is_array($data) ? $data : iterator_to_array($data);
        $html = '<style>
            .my-table { border-collapse: collapse; width: 100%; }
            .my-table th, .my-table td { border: 1px solid #000; padding: 5px; }
        </style>';
        $html .= '<table class="my-table"><thead><tr>';
        foreach ($columns as $col) {
            $html .= '<th>' . e($col) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($columns as $col) {
                $html .= '<td>' . e($row[$col] ?? '') . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        $pdf = Pdf::loadHTML($html);
        $pdfContent = $pdf->output();
        $exportPath = 'exports/' . basename($path);
        Storage::disk(config('filesystems.default', 'local'))->put($exportPath, $pdfContent);
        return $exportPath;
    }
}
