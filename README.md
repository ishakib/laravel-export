
# Laravel Export

Reusable, production-ready Laravel package for exporting large datasets (CSV/PDF), with async jobs and status tracking. Supports exporting millions of records efficiently.

## What You Can Achieve
- Export up to 1 million+ records to CSV or PDF without memory issues
- Run exports asynchronously using Laravel jobs/queues
- Track export status and progress
- Easily extend for custom data sources and formats
- Integrate with any Laravel app (auto-discovery supported)

## Features
- Export data to CSV and PDF
- Handles huge datasets (tested with 1M+ rows)
- Asynchronous export jobs (Laravel queues)
- Status tracking for exports
- Extensible export services and exporters
- Artisan command generator for new export services

## Installation

Install via Composer:

```bash
composer require vendor/laravel-export
```

Publish config (optional):

```bash
php artisan vendor:publish --tag=config
```

## Usage Example

### 1. Create an Export Service

Generate a new export service:

```bash
php artisan make:export User
```

This creates `app/Exports/UserExportService.php`:

```php
namespace App\Exports;

use Vendor\Export\ExportService;

class UserExportService extends ExportService
{
    protected function getResourceName(): string
    {
        return 'users';
    }
    protected function getData(array $columns): array
    {
        // Use chunked queries for large datasets
        return \App\Models\User::select($columns)->cursor();
    }
}
```

### 2. Trigger an Export (Controller Example)

```php
use App\Exports\UserExportService;
use Vendor\Export\CsvExporter;

public function exportUsers()
{
    $service = new UserExportService(new CsvExporter());
    $columns = ['id', 'name', 'email'];
    $type = 'csv';
    $userId = auth()->id();
    $export = $service->createExport($columns, $type, $userId);
    return response()->json(['export_id' => $export->id, 'status' => $export->status]);
}
```

### 3. Handling Large Exports with Jobs

The package uses Laravel jobs for async export. Ensure your queue is configured:

```bash
php artisan queue:work
```

Exports are processed in the background, chunking data to avoid memory issues. You can safely export 1M+ records.

### 4. Check Export Status

```php
$export = Export::find($id);
return response()->json(['status' => $export->status]);
```

## Extending

- Implement `ExporterInterface` for custom formats
- Extend `ExportService` for new resources

## Configuration

See `config/export.php` for disk, path, and format options.

## Contributing
Pull requests are welcome. For major changes, please open an issue first.

## License
MIT
