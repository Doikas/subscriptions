<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\EmailLog;

use App\Models\Subscription;
use App\Models\EmailLog;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
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

class EmailLogListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'subscription_email_logs';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            
            TD::make('subject', __('Subject'))
                ->sort()
                ->cantHide(),

            TD::make('subscription.customer.email', __('Sent To'))
                ->render(function (EmailLog $log) {
                    return $log->subscription->customer->email;
                })
                ->sort()
                ->cantHide(),

            TD::make('subscription.service.name', __('Service Name'))
                ->render(function (EmailLog $log) {
                    return $log->subscription->service->name;
                })
                ->sort()
                ->cantHide(),

            TD::make('subscription.customer.fullname', __('Customer Name'))
                ->render(function (EmailLog $log) {
                    return $log->subscription->customer->fullname;
                })
                ->sort()
                ->cantHide(),

            TD::make('subscription.domain', __('Domain'))
                ->sort()
                ->cantHide(),

            TD::make('subscription.expired_date', __('Expired Date'))
                ->sort()
                ->cantHide(),
                
            TD::make('sent_at', __('Sent At'))
                ->sort()
                ->cantHide(),

            TD::make('sent_successfully', __('Sent Successfully'))
                ->render(function (EmailLog $log) {
                    return $log->sent_successfully ? __('✓') : __('✖');
                })
                ->sort()
                ->cantHide(),
        ];
    }
}
