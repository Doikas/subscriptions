<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Subscription;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\DateTimer;
use Carbon\Carbon;
use Orchid\Screen\Layouts\Rows;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\CheckBox;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Subscription;


class SubscriptionEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     * 
     * 
     */
    public function fields(): array
    {

        $selectedServiceId = $this->query->get('subscription.service_id');
        $selectedService = Service::find($selectedServiceId);
        $serviceExpiration = optional($selectedService)->expiration;

        // Calculate the default date based on service expiration
        $defaultDate = $serviceExpiration
            ? Carbon::now('Europe/Athens')->addYear($serviceExpiration)
            : Carbon::now('Europe/Athens')->addYear(); // Default to 1 year if no service selected

        // $yearexpired = 'service.expiration';
        $toda = Carbon::now('Europe/Athens');
        $currentDate = Carbon::now('Europe/Athens');
        //$defaultDate = $currentDate->add(1, 'year');
        // $defaultDate = $currentDate->add($yearexpired, 'year');
        $customers = Customer::all();
    $customerOptions = [];

    foreach ($customers as $customer) {
        $customerOptions[$customer->id] = "{$customer->firstname} {$customer->lastname}";
    }

        
        return [
            Relation::make('subscription.service_id')
                ->fromModel(Service::class, 'name', 'id')
                ->title(__('Service'))
                ->chunk(100)
                ->id('custom-service-field'),

            Relation::make('subscription.customer_id')
                ->fromModel(Customer::class, 'fullname', 'id')
                ->title(__('Customer')),

            Input::make('subscription.domain')
                ->type('text')
                ->max(255)
                ->title(__('Domain'))
                ->placeholder(__('Domain')),

            Input::make('subscription.price')
                ->type('float')
                ->title(__('Price'))
                ->placeholder(__('Price')),

            // Checkbox::make('subscription.paid_status')
            //         ->title(__('Paid Status'))
            //         ->sendTrueOrFalse(),

            DateTimer::make('subscription.start_date')
                    ->title('Start Date')
                    ->value($toda)
                    ->allowInput()
                    ->enableTime(),

            DateTimer::make('subscription.expired_date')
                    ->title('Expired Date')
                    ->value($defaultDate)
                    ->allowInput()
                    ->enableTime()
                    ->id('custom-date-field'),

            Input::make('subscription.notes')
                ->type('text')
                ->max(400)
                ->title(__('Notes'))
                ->placeholder(__('Notes')),
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