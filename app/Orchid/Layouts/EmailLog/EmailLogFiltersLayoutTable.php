<?php

namespace App\Orchid\Layouts\EmailLog;

use App\Orchid\Filters\EmailLogFilter;
use App\Orchid\Filters\EmailLogSortTable;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class EmailLogFiltersLayoutTable extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            EmailLogSortTable::class,
        ];
    }
}