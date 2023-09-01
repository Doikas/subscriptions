<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use App\Models\Customer;
use Orchid\Screen\Fields\Select;

class CustomerFilter extends Filter
{
    /**
     * @return string
     */
    public $parameters = ['fullname'];

    public function fullname(): string
    {
        return 'Fullname';
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        $selectedFullName = $this->request->get('fullname');

        // Split the full name into first name and last name
        [$firstName, $lastName] = explode(' ', $selectedFullName, 2);

        // Query the database to find the customer with the matching first name and last name
        return $builder->where('firstname', $firstName)->where('lastname', $lastName);
    }

    /**
     * @return array
     */
    public function display(): array
    {
        $customers = Customer::all();
        $customerOptions = [];

        foreach ($customers as $customer) {
            $fullName = "{$customer->firstname} {$customer->lastname}";
            $customerOptions[$fullName] = $fullName;
        }

        return [
            Select::make('fullname')
                ->options($customerOptions)
                ->empty()
                ->value($this->request->get('fullname'))
                ->title(__('Fullname')),
        ];
    }

    /**
     * @return string
     */
    public function value(): string
    {
        $selectedFullName = $this->request->get('fullname');
        return $this->fullname() . ': ' . $selectedFullName;
    }
}
