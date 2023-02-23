<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Customer;

use App\Models\Customer;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class CustomerListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'customers';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('firstname', __('First Name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('lastname', __('Last Name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('email', __('Email'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn (Customer $customer) => ModalToggle::make($customer->email)
                    ->modal('asyncEditCustomerModal')
                    ->modalTitle($customer->firstname." ".$customer->lastname)
                    ->method('saveCustomer')
                    ->asyncParameters([
                        'customer' => $customer->id,
                    ])),

            TD::make('website', __('Website'))
                    ->sort()
                    ->cantHide()
                    ->filter(Input::make()),
                    
            TD::make('pronunciation', __('Pronunciation'))
                    ->sort()
                    ->cantHide()
                    ->filter(Input::make()),     

            TD::make('phone', __('Phone'))
                    ->sort()
                    ->filter(Input::make()),

            TD::make('notes', __('Notes')),
                    

            // TD::make('updated_at', __('Last edit'))
            //     ->sort()
            //     ->render(fn (Customer $customer) => $customer->updated_at->toDateTimeString()),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Customer $customer) => DropDown::make()
                    ->icon('options-vertical')
                    ->list([

                        Link::make(__('Edit'))
                            ->route('platform.systems.customers.edit', $customer->id)
                            ->icon('pencil'),

                        Button::make(__('Delete'))
                            ->icon('trash')
                            ->confirm(__('Once the customer is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            ->method('remove', [
                                'id' => $customer->id,
                            ]),
                    ])),
        ];
    }
}