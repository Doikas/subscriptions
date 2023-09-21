<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use App\Models\Customer;
use App\Models\Subscription;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Input;

class CustomerSortFullname extends Filter
{
    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return [];
    }

    /**
     * Apply to a given Eloquent query builder.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        $sort = $this->request->get('sort', '');
        if (str_contains($sort, 'customer.fullname')) {
            $direction = str_starts_with($sort, '-')? 'desc' : 'asc';
            return $builder->orderBy(Customer::select('fullname')
                ->whereColumn('customers.id', 'subscriptions.customer_id')
                ->orderBy('fullname')
                ->limit(1), $direction);
        } else {
            return $builder;
        }
    }

    public function render(): string
{
    // Since you don't need a custom render for this filter, return an empty string.
    return '';
}
}