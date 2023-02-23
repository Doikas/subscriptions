<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Service;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
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

            Input::make('service.description')
                ->type('text')
                ->max(255)
                ->title(__('Description'))
                ->placeholder(__('Description')),

            Input::make('service.expiration')
                ->type('date')
                ->title(__('Expiration Date'))
                ->placeholder(__('Expiration Date')),
        ];
    }
}