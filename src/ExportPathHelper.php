<?php

namespace Vendor\Export;

trait ExportPathHelper
{
    protected function getExportFilePath(string $path): string
    {
        return storage_path('app/exports/' . basename($path));
    }

    protected function getExportReturnPath(string $path): string
    {
        return 'exports/' . basename($path);
    }
}
