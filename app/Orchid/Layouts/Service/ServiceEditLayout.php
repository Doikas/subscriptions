<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Service;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\DateTimer;
use Carbon\Carbon;
use Orchid\Screen\Layouts\Rows;

class ServiceEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        
        return [
            Input::make('service.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Service Name'))
                ->placeholder(__('Service Name')),

            Input::make('service.slug')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Slug'))
                ->placeholder(__('Slug')),

            Input::make('service.description')
                ->type('text')
                ->max(255)
                ->title(__('Description'))
                ->placeholder(__('Description')),

            Input::make('service.expiration')
                ->type('number')
                ->title(__('Expiration Number of Years'))
                ->placeholder(__('Years')),
        ];
    }
}
// $currentDate = Carbon::now('Europe/Athens');
        // $defaultDate = $currentDate->add(1, 'year');

// DateTimer::make('service.expiration')
//                 ->title('Expiration Date')
//                 ->value($defaultDate)
//                 ->allowInput()
//                 ->enableTime(),

//DateTimer::make() sto list arxeio