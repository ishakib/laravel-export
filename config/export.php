<?php
return [
    // Disk to use for exports
    'default_disk' => function_exists('env') ? env('EXPORT_DISK', 'local') : 'local',
    // Path relative to storage/app
    'path' => function_exists('env') ? env('EXPORT_PATH', 'exports') : 'exports',
    // Supported formats
    'formats' => ['csv', 'pdf'],
];
