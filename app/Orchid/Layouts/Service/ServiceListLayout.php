<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Service;

use App\Models\Service;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ServiceListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'services';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', __('Service Name'))
                ->sort()
                ->cantHide()
                ->render(fn (Service $service) => ModalToggle::make($service->name)
                    ->modal('asyncEditServiceModal')
                    ->modalTitle($service->name)
                    ->method('saveService')
                    ->asyncParameters([
                        'service' => $service->id,
                    ])),

            TD::make('slug', __('Slug'))
                ->sort()
                ->cantHide(),

            TD::make('description', __('Service Description'))
                ->sort()
                ->cantHide(),

            TD::make('expiration', __('Expiry Years'))
                    ->sort()
                    ->cantHide(),
                    

            // TD::make('updated_at', __('Last edit'))
            //     ->sort()
            //     ->render(fn (Customer $customer) => $customer->updated_at->toDateTimeString()),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Service $service) => Group::make([

                        Link::make(__('Edit'))
                            ->route('platform.systems.services.edit', $service->id)
                            ->icon('pencil'),

                        Button::make(__('Delete'))
                            ->icon('trash')
                            ->confirm(__('Once the service is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            ->method('remove', [
                                'id' => $service->id,
                            ]),
                    ])),
        ];
    }
}