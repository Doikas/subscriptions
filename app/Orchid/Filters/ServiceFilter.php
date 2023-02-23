<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use App\Models\Service;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;


class ServiceFilter extends Filter
{
    /**
     * @return string
     */

    public $parameters = ['name'];

    public function name(): string
    {
        return 'Name';
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
        return $builder->where('slug', $this->request->get('name'));
    }

    /**
     * @return Field[]
     */
    public function display(): array
    {
        return [
            Select::make('name')
                ->fromModel(Service::class, 'name', 'slug')
                ->empty()
                ->value($this->request->get('name'))
                ->title(__('Name')),
        ];
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->name(). ': '.Service::where('slug', $this->request->get('name'))->first()->name;
    }
}
