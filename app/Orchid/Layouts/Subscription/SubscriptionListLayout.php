<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Subscription;

use App\Models\Subscription;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use App\Models\Service;
use App\Models\Customer;


class SubscriptionListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'subscriptions';


    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('service.name', __('Service Name'))
                ->sort()
                ->cantHide(),

            TD::make('customer.fullname', __('Customer Name'))
                ->cantHide()
                ->sort(),

            TD::make('customer.email', __('Customer Email'))
                ->sort()
                ->cantHide(),

            TD::make('domain', __('Domain'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('price', __('Price'))
                    ->sort()
                    ->cantHide()
                    ->filter(Input::make()),

            // TD::make('paid_status', __('Paid Status'))
            // ->render(function ($checkbox){
            //     foreach($checkbox as $checkbox){
                
            //     if ($checkbox==true){return "Paid";} return "Unpaid";
            // }}),

            TD::make('start_date', __('Start Date'))
                    ->sort()
                    ->cantHide()
                    ->filter(DateTimer::make()),

            TD::make('expired_date', __('Expired Date'))
                    ->sort()
                    ->cantHide()
                    ->filter(DateTimer::make()),

            TD::make('notes', __('Notes'))
                    ->sort()
                    ->cantHide()
                    ->filter(Input::make()),
                    

            // TD::make('updated_at', __('Last edit'))
            //     ->sort()
            //     ->render(fn (Customer $customer) => $customer->updated_at->toDateTimeString()),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Subscription $subscription) => DropDown::make()
                    ->icon('options-vertical')
                    ->list([

                        Link::make(__('Edit'))
                            ->route('platform.systems.subscriptions.edit', $subscription->id)
                            ->icon('pencil'),

                        Button::make(__('Delete'))
                            ->icon('trash')
                            ->confirm(__('Once the service is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            ->method('remove', [
                                'id' => $subscription->id,
                            ]),
                    ])),
        ];
    }
}