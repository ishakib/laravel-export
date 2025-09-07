<?php

namespace Vendor\Export;

use App\Models\User;

class UserExportService extends AbstractExportService
{
    public function getData(array $columns): iterable
    {
        return User::select($columns)->cursor();
    }
    public function transformRow($item, array $columns): array
    {
        return collect($columns)->mapWithKeys(fn($col) => [$col => $item->{$col} ?? ''])->toArray();
    }
}
