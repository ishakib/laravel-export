<?php

namespace Vendor\Export;

interface ExporterInterface
{
    /**
     * Export data to a file.
     * @param iterable $data
     * @param array $columns
     * @param string $path
     * @param string $format Must be 'csv' or 'pdf'
     * @return string
     */
    public function export(iterable $data, array $columns, string $path, string $format): string;
}
