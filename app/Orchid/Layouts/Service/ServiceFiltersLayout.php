<?php

namespace App\Orchid\Layouts\Service;

use App\Orchid\Filters\ServiceFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class ServiceFiltersLayout extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            ServiceFilter::class,
        ];
    }
}