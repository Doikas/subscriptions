<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Customer;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class CustomerEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('customer.firstname')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('First Name'))
                ->placeholder(__('First Name')),

            Input::make('customer.lastname')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Last Name'))
                ->placeholder(__('Last Name')),

            Input::make('customer.email')
                ->type('email')
                ->required()
                ->title(__('Email'))
                ->placeholder(__('Email')),

            Input::make('customer.website')
                ->type('text')
                ->max(255)
                ->title(__('Website'))
                ->placeholder(__('Website')),

            Input::make('customer.pronunciation')
                ->type('text')
                ->max(255)
                ->title(__('Pronunciation'))
                ->placeholder(__('Pronunciation')),

            Input::make('customer.phone')
                ->type('text')
                ->max(255)
                ->title(__('Phone'))
                ->placeholder(__('Phone')),

            Input::make('customer.notes')
                ->type('text')
                ->max(400)
                ->title(__('Notes'))
                ->placeholder(__('Notes')),
        ];
    }
}