<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use App\Models\Customer;
use App\Models\Subscription;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Input;
use App\Models\Service;

class SubscriptionFilter extends Filter
{
    public $parameters = ['customer', 'domain', 'email', 'service'];

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

    public function service(): string
    {
        return 'Service';
    }

    public function run(Builder $builder): Builder
{
    $customerFilter = $this->request->get('customer');
    $domainFilter = $this->request->get('domain');
    $emailFilter = $this->request->get('email');
    $serviceFilter = $this->request->get('service');
    $subscriptionFilter = $this->request->get('subscription');

    // Join the Customer table to access the email field
    $builder = $builder
        ->join('customers', 'subscriptions.customer_id', '=', 'customers.id')
        ->join('services', 'subscriptions.service_id', '=', 'services.id')
        ->select('subscriptions.*', 'customers.email', 'services.name');


    // Start with a base query that always includes the customer filter
    if (!empty($customerFilter)) {
        $builder->where('customers.id', $customerFilter);
    }
    // Add conditions for domain and email filters using "orWhere" to allow for multiple filters
    if (!empty($domainFilter)) {
        $builder = $builder->orWhere('subscriptions.domain', $domainFilter);
    }

    if (!empty($emailFilter)) {
        $builder = $builder->orWhere('customers.email', $emailFilter);
    }
    if (!empty($serviceFilter)) {
        $builder->where('services.id', $serviceFilter);
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
        $services = Service::all();
        $serviceOptions = [];

        foreach ($services as $service) {
            $serviceOptions[$service->id] = $service->name;
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
            Relation::make('customer')
               ->fromModel(Customer::class, 'fullname') // Assuming 'fullname' is a field in the Customer model representing the full name.
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

            Relation::make('service')
                ->fromModel(Service::class, 'name')
                ->chunk(20)
                ->title(__('Service')),
        ];
    }

    public function value(): string
{
    $selectedCustomerId = $this->request->get('customer');
    $selectedDomain = $this->request->get('domain');
    $selectedEmail = $this->request->get('email');
    $selectedServiceId = $this->request->get('service');
    
    $customer = Customer::find($selectedCustomerId);
    $service = Service::find($selectedServiceId);
    $servicename = $service ? $service->name : '';
    $fullName = $customer ? "{$customer->firstname} {$customer->lastname}" : '';
    
    $email = $selectedEmail; // Email doesn't require a database lookup.
    
    $value = '';

    if ($customer) {
        // Only include customer information if neither domain nor email is selected.
        $value .= $this->customer() . ': ' . $fullName;
    }

    if($service) {
        if (!empty($value)) {
            $value .= ', ';
        }
        $value .= $this->service() . ': ' . $servicename;
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
