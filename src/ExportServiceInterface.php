<?php

namespace Vendor\Export;

interface ExportServiceInterface
{
    public function getData(array $columns): iterable;
    public function transformRow($item, array $columns): array;
}
