<?php

namespace App\Orchid\Layouts\EmailLog;

use App\Orchid\Filters\EmailLogFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class EmailLogFiltersLayout extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            EmailLogFilter::class,
        ];
    }
}