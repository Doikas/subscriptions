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
    public $parameters = ['customer', 'domain']; // Add 'domain' parameter

    public function customer(): string
    {
        return 'Customer';
    }

    public function domain(): string // Define a title for the domain filter
    {
        return 'Domain';
    }

    public function run(Builder $builder): Builder
{
    $customerFilter = $this->request->get('customer');
    $domainFilter = $this->request->get('domain');

    if (!empty($customerFilter)) {
        $builder = $builder->where('customer_id', $customerFilter);
    }

    if (!empty($domainFilter)) {
        $builder = $builder->where('domain', $domainFilter);
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

    $domainOptions = [];

    if (!$this->request->filled('customer')) {
        // If no customer is selected, fetch all domains
        $allDomains = Subscription::pluck('domain')->unique();
        foreach ($allDomains as $domain) {
            $domainOptions[$domain] = $domain;
        }
    } elseif ($this->request->filled('customer')) {
        // If a customer is selected, fetch only the domains related to that customer
        $selectedCustomerId = $this->request->get('customer');
        $selectedCustomerDomains = Subscription::where('customer_id', $selectedCustomerId)
            ->pluck('domain')->unique();

        foreach ($selectedCustomerDomains as $domain) {
            $domainOptions[$domain] = $domain;
        }
    }

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
    ];
}



public function value(): string
{
    $selectedCustomerId = $this->request->get('customer');
    $selectedDomain = $this->request->get('domain');
    
    $customer = Customer::where('id', $selectedCustomerId)->first();
    $fullName = $customer ? "{$customer->firstname} {$customer->lastname}" : '';
    $email = $customer ? $customer->email : '';
    
    $value = $this->customer() . ': ' . $fullName;

    if ($selectedDomain) {
        $value .= ', Domain: ' . $selectedDomain;
    }

    if ($email) {
        $value .= ', Email: ' . $email;
    }

    return $value;
}


}
