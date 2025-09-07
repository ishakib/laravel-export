<?php

namespace Vendor\Export;

use Illuminate\Support\ServiceProvider;
use Vendor\Export\Console\MakeExportCommand;

class ExportServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/export.php', 'export');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/export.php' => config_path('export.php'),
            ], 'config');
            $this->commands([
                MakeExportCommand::class,
            ]);
        }
    }
}
