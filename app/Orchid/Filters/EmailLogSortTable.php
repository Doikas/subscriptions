<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use App\Models\EmailLog;
use App\Models\Customer;
use App\Models\Subscription;
use App\Models\Service;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Input;

class EmailLogSortTable extends Filter
{
    public function parameters(): ?array
    {
        return [];
    }

    public function run(Builder $builder): Builder
    {
        $sort = $this->request->get('sort', '');

        if (str_contains($sort, 'subscription.customer.fullname')) {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';

            return $builder->join('subscriptions', 'subscription_email_logs.subscription_id', '=', 'subscriptions.id')
                ->join('customers', 'subscriptions.customer_id', '=', 'customers.id')
                ->orderBy('customers.fullname', $direction);
        } elseif (str_contains($sort, 'subscription.domain')) {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';

            return $builder->join('subscriptions', 'subscription_email_logs.subscription_id', '=', 'subscriptions.id')
                ->orderBy('subscriptions.domain', $direction);
        } elseif (str_contains($sort, 'subscription.expired_date')) {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';

            return $builder->join('subscriptions', 'subscription_email_logs.subscription_id', '=', 'subscriptions.id')
                ->orderBy('subscriptions.expired_date', $direction);
        } elseif (str_contains($sort, 'subscription.customer.email')) {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';

            return $builder->join('subscriptions', 'subscription_email_logs.subscription_id', '=', 'subscriptions.id')
                ->join('customers', 'subscriptions.customer_id', '=', 'customers.id')
                ->orderBy('customers.email', $direction);
        } elseif (str_contains($sort, 'subscription.service.name')) {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';

            return $builder->join('subscriptions', 'subscription_email_logs.subscription_id', '=', 'subscriptions.id')
                ->join('services', 'subscriptions.service_id', '=', 'services.id')
                ->orderBy('services.name', $direction);
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