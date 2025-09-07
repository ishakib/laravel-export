<?php

namespace Vendor\Export;

abstract class AbstractExportService implements ExportServiceInterface
{
    protected ExporterInterface $exporter;

    public function __construct(ExporterInterface $exporter)
    {
        $this->exporter = $exporter;
    }

    abstract public function getData(array $columns): iterable;
    abstract public function transformRow($item, array $columns): array;

    public function validateColumns(array $columns, array $valid): array
    {
        return array_diff($columns, $valid);
    }

    public function generateFilename(string $type, string $ext): string
    {
        return $type . '-' . date('Ymd_His') . '.' . $ext;
    }

    public function export(iterable $data, array $columns, string $path, string $format): string
    {
        return $this->exporter->export($data, $columns, $path, $format);
    }
}
