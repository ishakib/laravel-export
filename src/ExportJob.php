<?php

namespace Vendor\Export;

use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Export $export;

    public function __construct(Export $export)
    {
        $this->export = $export;
    }

    public function handle()
    {
        $service = $this->resolveService($this->export->resource, $this->export->type);
        $path = $service->processExport($this->export);
        $disk = config('filesystems.default', 'local');
        $url = $this->generateUrl($disk, $path);
        $this->export->update([
            'status' => 'completed',
            'file_path' => $path,
            'url' => $url,
        ]);
    }

    protected function resolveService(string $resource, string $type): object
    {
        if ($resource === 'users') {
            $exporter = $type === 'csv'
                ? new CsvExporter()
                : new PdfExporter();
            return new UserExportService($exporter);
        }
        if ($resource === 'role-permissions') {
            $exporter = $type === 'csv'
                ? new CsvExporter()
                : new PdfExporter();
            return new RolePermissionExportService($exporter);
        }
        throw new \InvalidArgumentException('Unknown export resource: ' . $resource);
    }

    protected function generateUrl(string $disk, string $path): string
    {
        if ($disk === 'local') {
            return url('/storage/' . basename($path));
        }
        // For disks that do not support url(), fallback to path
        // You may customize this for S3 or other disks as needed
        return $path;
    }
}
