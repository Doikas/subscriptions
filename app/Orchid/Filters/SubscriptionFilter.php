<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use App\Models\Customer;
use App\Models\Subscription;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Input;

class SubscriptionFilter extends Filter
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

    // Join the Customer table to access the email field
    $builder = $builder->join('customers', 'subscriptions.customer_id', '=', 'customers.id');

    // Start with a base query that always includes the customer filter
    $builder = $builder->where('customers.id', $customerFilter);

    // Add conditions for domain and email filters using "orWhere" to allow for multiple filters
    if (!empty($domainFilter)) {
        $builder = $builder->orWhere('subscriptions.domain', $domainFilter);
    }

    if (!empty($emailFilter)) {
        $builder = $builder->orWhere('customers.email', $emailFilter);
    }

    return $builder;
}


    public function display(): array
    {
        $customers = Customer::all();
        $customerOptions = [];

        foreach ($customers as $customer) {
            $customerOptions[$customer->id] = "{$customer->firstname} {$customer->lastname}";
        }

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
                ->value($this->request->get('customer'))
                ->title(__('Customer')),
            
            Select::make('domain')
                ->options($domainOptions)
                ->empty()
                ->value($this->request->get('domain'))
                ->title(__('Domain')),
            
            Select::make('email')
                ->options($emailOptions)
                ->empty()
                ->value($this->request->get('email'))
                ->title(__('Email')),
        ];
    }

    public function value(): string
{
    $selectedCustomerId = $this->request->get('customer');
    $selectedDomain = $this->request->get('domain');
    $selectedEmail = $this->request->get('email');
    
    $customer = Customer::find($selectedCustomerId);
    $fullName = $customer ? "{$customer->firstname} {$customer->lastname}" : '';
    
    $email = $selectedEmail; // Email doesn't require a database lookup.
    
    $value = '';

    if ($customer) {
        // Only include customer information if neither domain nor email is selected.
        $value .= $this->customer() . ': ' . $fullName;
    }

    if ($selectedDomain) {
        if (!empty($value)) {
            $value .= ', ';
        }
        $value .= 'Domain: ' . $selectedDomain;
    }

    if ($selectedEmail) {
        if (!empty($value)) {
            $value .= ', ';
        }
        $value .= 'Email: ' . $email;
    }

    return $value;
}

}

