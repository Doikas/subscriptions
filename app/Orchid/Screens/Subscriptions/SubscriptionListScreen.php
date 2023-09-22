<?php

namespace App\Orchid\Screens\Subscriptions;

use Orchid\Screen\Screen;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayout;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayoutEmail;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayoutFullname;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayoutServiceName;
use App\Orchid\Layouts\Subscription\SubscriptionEditLayout;
use App\Orchid\Layouts\Subscription\SubscriptionListLayout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use App\Models\Subscription;
use App\Models\Customer;
use App\Models\Service;
use GuzzleHttp\Psr7\Query;
use Orchid\Screen\TD;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionStatusNotification;
use App\Mail\ExpirationReminder30Days;
use App\Mail\ExpirationReminder5Days;
use App\Mail\ExpirationReminder0Days;


class SubscriptionListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            
            'subscriptions' => Subscription::with('customer','service')
                ->filters(SubscriptionFiltersLayout::class)
                ->filters(SubscriptionFiltersLayoutEmail::class)
                ->filters(SubscriptionFiltersLayoutFullname::class)
                ->filters(SubscriptionFiltersLayoutServiceName::class)
                ->defaultSort('expired_date', 'asc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Manage Subscriptions';
    }

    public function description(): ?string
    {
        return 'All Registered Subscriptions';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('plus')
                ->href(route('platform.systems.subscriptions.create')),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            SubscriptionFiltersLayout::class,
            SubscriptionListLayout::class,

            Layout::modal('asyncEditSubscriptionModal', SubscriptionEditLayout::class)
                ->async('asyncGetSubscription'),
            
        ];
    }

    public function asyncGetSubscription(Subscription $subscription): iterable
    {
        return [
            'subscription' => $subscription,
        ];
    }

    public function saveSubscription(Request $request, Subscription $subscription): void
    {

        $subscription->fill($request->input('subscription'))->save();

        Toast::info(__('Subscription was saved.'));
    }

    public function remove(Request $request): void
    {
        Subscription::findOrFail($request->get('id'))->delete();

        Toast::info(__('Subscription was removed'));
    }

    public function sendStatusEmail($id)
{
    // Retrieve the subscription by ID
    $subscription = Subscription::findOrFail($id);
    $expiredDate = Carbon::parse($subscription->expired_date);
    $present = Carbon::now('Europe/Athens');

    if($expiredDate->isFuture()){
    $daysUntilExpiration = $present->diffInDays($expiredDate);

    if ($daysUntilExpiration > 30){
        $data = [
            'customer.email' => $subscription->customer->email,
            'customer.pronunciation' => $subscription->customer->pronunciation,
            'service.name' => $subscription->service->name,
            'domain' => $subscription->domain,
            'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
        ];

        Mail::to($subscription->customer->email)
        ->cc('alexakis@wdesign.gr')
        ->send(new SubscriptionStatusNotification($data));
        Toast::info(__('Status email sent successfully'), 'success');
    }elseif ($daysUntilExpiration <= 30 && $daysUntilExpiration > 5) {
        $data = [
            'customer.email' => $subscription->customer->email,
            'customer.pronunciation' => $subscription->customer->pronunciation,
            'service.name' => $subscription->service->name,
            'domain' => $subscription->domain,
            'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
        ];

        Mail::to($subscription->customer->email)
        ->cc('alexakis@wdesign.gr')
        ->send(new ExpirationReminder30Days($data));
        Toast::info(__('Status email sent successfully'), 'success');
    }elseif ($daysUntilExpiration <= 5 && $daysUntilExpiration > 0) {
        $data = [
            'customer.email' => $subscription->customer->email,
            'customer.pronunciation' => $subscription->customer->pronunciation,
            'service.name' => $subscription->service->name,
            'domain' => $subscription->domain,
            'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
        ];

        Mail::to($subscription->customer->email)
        ->cc('alexakis@wdesign.gr')
        ->send(new ExpirationReminder5Days($data));
        Toast::info(__('Status email sent successfully'), 'success');
    }elseif ($daysUntilExpiration == 0 || $daysUntilExpiration < 0 ) {
        $data = [
            'customer.email' => $subscription->customer->email,
            'customer.pronunciation' => $subscription->customer->pronunciation,
            'service.name' => $subscription->service->name,
            'domain' => $subscription->domain,
            'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
        ];

        Mail::to($subscription->customer->email)
        ->cc('alexakis@wdesign.gr')
        ->send(new ExpirationReminder0Days($data));
        Toast::info(__('Status email sent successfully'), 'success');
    }
}else {
    $data = [
        'customer.email' => $subscription->customer->email,
        'customer.pronunciation' => $subscription->customer->pronunciation,
        'service.name' => $subscription->service->name,
        'domain' => $subscription->domain,
        'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
    ];

    Mail::to($subscription->customer->email)
    ->cc('alexakis@wdesign.gr')
    ->send(new ExpirationReminder0Days($data));
    Toast::info(__('Status email sent successfully'), 'success');
}

    return back();
}
}
