<?php

namespace App\Orchid\Layouts\Customer;

use App\Orchid\Filters\CustomerFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class CustomerFiltersLayout extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            CustomerFilter::class,
        ];
    }
}
