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
    public function email(): string
    {
        return __('Email');
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['email'];
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        return $builder->where('email', $this->request->get('email')
        );
    }

    /**
     * @return Field[]
     */
    public function display(): array
    {
        return [
            Select::make('email')
                ->fromModel(Customer::class, 'email')
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
        return $this->name().': '.Customer::where($this->request->get('email'))->first()->name;
    }
}
