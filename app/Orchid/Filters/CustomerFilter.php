<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use App\Models\Customer;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;


class CustomerFilter extends Filter
{
    /**
     * @return string
     */

    public $parameters = ['email'];

    public function email(): string
    {
        return 'Email';
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        return $builder->where('id', $this->request->get('email'));
    }

    /**
     * @return Field[]
     */
    public function display(): array
    {
        return [
            Select::make('email')
                ->fromModel(Customer::class, 'email', 'id')
                ->empty()
                ->value($this->request->get('email'))
                ->title(__('Email')),
        ];
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->email(). ': '.Customer::where('id', $this->request->get('email'))->first()->firstname;
    }
}
