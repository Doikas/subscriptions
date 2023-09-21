<?php

namespace App\Orchid\Layouts\Subscription;

use App\Orchid\Filters\SubscriptionFilter;
use App\Orchid\Filters\CustomerSortEmail;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;
use Orchid\Screen\Layout;
use Orchid\Screen\Fields\Input;

class SubscriptionFiltersLayoutEmail extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            CustomerSortEmail::class,
        ];
    }
    
}