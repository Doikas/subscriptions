<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use App\Models\EmailLog;
use App\Models\Customer;
use App\Models\Subscription;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Input;

class EmailLogFilter extends Filter
{
    public $parameters = ['customer', 'domain', 'email'];

    public function customer(): string
    {
        return 'Customer';
    }

    public function domain(): string
    {
        return 'Domain';
    }

    public function email(): string
    {
        return 'Email';
    }

    public function run(Builder $builder): Builder
    {
        $customerFilter = $this->request->get('customer');
        $domainFilter = $this->request->get('domain');
        $emailFilter = $this->request->get('email');

        // Start with a base query that always includes the customer filter
        if (!empty($customerFilter)) {
            $builder = $builder->whereHas('subscription.customer', function ($query) use ($customerFilter) {
                $query->where('id', $customerFilter);
            });
        }

        // Check if domain filter is provided
        if (!empty($domainFilter)) {
            $builder = $builder->whereHas('subscription', function ($query) use ($domainFilter) {
                $query->where('domain', $domainFilter);
            });
        }

        // Check if email filter is provided
        if (!empty($emailFilter)) {
            $builder = $builder->whereHas('subscription.customer', function ($query) use ($emailFilter) {
                $query->where('email', $emailFilter);
            });
        }

        return $builder;
    }

    public function display(): array
    {
        $customers = Customer::all();
        $customerOptions = $customers->pluck('full_name', 'id')->prepend(__('All Customers'), '');

        $allDomains = Subscription::pluck('domain')->unique();
        $domainOptions = $allDomains->mapWithKeys(function ($domain) {
            return [$domain => $domain];
        });

        $allEmails = Customer::pluck('email')->unique();
        $emailOptions = $allEmails->mapWithKeys(function ($email) {
            return [$email => $email];
        });

        return [
            Select::make('customer')
                ->options($customerOptions)
                ->empty()
                ->title(__('Customer')),
            
            Select::make('domain')
                ->options($domainOptions)
                ->empty()
                ->title(__('Domain')),
            
            Select::make('email')
                ->options($emailOptions)
                ->empty()
                ->title(__('Email')),
        ];
    }

    public function value(): string
    {
        $selectedCustomerId = $this->request->get('customer');
        $selectedDomain = $this->request->get('domain');
        $selectedEmail = $this->request->get('email');

        $value = [];

        if ($selectedCustomerId) {
            $customer = Customer::find($selectedCustomerId);
            $fullName = $customer ? $customer->full_name : '';
            $value[] = "{$this->customer()}: $fullName";
        }

        if ($selectedDomain) {
            $value[] = "{$this->domain()}: $selectedDomain";
        }

        if ($selectedEmail) {
            $value[] = "{$this->email()}: $selectedEmail";
        }

        return implode(', ', $value);
    }
}


