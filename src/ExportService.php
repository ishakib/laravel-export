<?php

namespace Vendor\Export;

use App\Models\Export;

abstract class ExportService
{
    protected ExporterInterface $exporter;
    public function __construct(ExporterInterface $exporter)
    {
        $this->exporter = $exporter;
    }
    abstract protected function getResourceName(): string;
    abstract protected function getData(array $columns): array;
    public function createExport(array $columns, string $type, int $userId): Export
    {
        $export = Export::create([
            'user_id' => $userId,
            'resource' => $this->getResourceName(),
            'columns' => json_encode($columns),
            'type' => $type,
            'status' => 'pending',
        ]);
        \App\Jobs\ExportJob::dispatch($export);
        return $export;
    }
    public function processExport(Export $export): string
    {
        $columns = json_decode($export->columns, true);
        $data = $this->getData($columns);
        $filename = $this->getResourceName() . '-' . date('Y-m-d_H-i-s') . '.' . $export->type;
        $path = 'exports/' . $filename;
        $this->exporter->export($data, $columns, $path, $export->type);
        $export->update([
            'status' => 'completed',
            'file_path' => $path,
        ]);
        return $path;
    }
}
