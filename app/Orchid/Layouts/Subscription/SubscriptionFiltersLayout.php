<?php

namespace App\Orchid\Layouts\Subscription;

use App\Orchid\Filters\SubscriptionFilter;
use App\Orchid\Filters\SubscriptionFiltersLayoutTable;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class SubscriptionFiltersLayout extends Selection
{
    /**
     * @return string[]|Filter[]
     */
    public function filters(): array
    {
        return [
            SubscriptionFilter::class,
        ];
    }
}