<?php

namespace Vendor\Export;

use App\Models\Role;

class RolePermissionExportService extends AbstractExportService
{
    public function getResourceName(): string
    {
        return 'role-permissions';
    }
    public function getData(array $columns): iterable
    {
        return Role::with('permissions')->cursor();
    }
    public function transformRow($item, array $columns): array
    {
        $row = [];
        foreach ($columns as $col) {
            $row[$col] = $col === 'permissions'
                ? $item->permissions->pluck('name')->implode(', ')
                : ($item->{$col} ?? '');
        }
        return $row;
    }
}
