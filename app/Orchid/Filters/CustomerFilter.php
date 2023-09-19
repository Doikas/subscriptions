<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use App\Models\Customer;
use Orchid\Screen\Fields\Select;

class CustomerFilter extends Filter
{
    public $parameters = ['fullname', 'email'];

    public function fullname(): string
    {
        return 'Fullname';
    }

    public function email(): string
    {
        return 'Email';
    }

    public function run(Builder $builder): Builder
    {
        $selectedFullName = $this->request->get('fullname');
        $emailFilter = $this->request->get('email');

        if ($selectedFullName) {
            [$firstName, $lastName] = explode(' ', $selectedFullName, 2);
            $builder->where('firstname', $firstName)->where('lastname', $lastName);
        }

        if (!empty($emailFilter)) {
            $builder->orWhere('email', $emailFilter);
        }

        return $builder;
    }

    public function display(): array
    {
        $customers = Customer::all();
        $customerOptions = [];

        foreach ($customers as $customer) {
            $fullName = "{$customer->firstname} {$customer->lastname}";
            $customerOptions[$fullName] = $fullName;
        }

        $allEmails = Customer::pluck('email')->unique();
        $emailOptions = $allEmails->mapWithKeys(function ($email) {
            return [$email => $email];
        });

        return [
            Select::make('fullname')
                ->options($customerOptions)
                ->empty()
                ->value($this->request->get('fullname'))
                ->title(__('Fullname')),

            Select::make('email')
                ->options($emailOptions)
                ->empty()
                ->value($this->request->get('email'))
                ->title(__('Email')),
        ];
    }

    public function value(): string
    {
        $selectedFullName = $this->request->get('fullname');
        $selectedEmail = $this->request->get('email');
        $value = '';

        if ($selectedFullName) {
            $value .= 'Customer: ' . $selectedFullName;
        }

        if ($selectedEmail) {
            if ($value) {
                $value .= ', ';
            }
            $value .= 'Email: ' . $selectedEmail;
        }

        return $value;
    }
}


