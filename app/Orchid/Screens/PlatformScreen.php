<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayoutEmail;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayoutFullname;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayoutServiceName;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayout;
use App\Orchid\Layouts\Subscription\SubscriptionEditLayout;
use App\Orchid\Layouts\Subscription\SubscriptionListLayout;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionStatusNotification;
use App\Mail\ExpirationReminder30Days;
use App\Mail\ExpirationReminder5Days;
use App\Mail\ExpirationReminder0Days;

class PlatformScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Wdesign Subscriptions';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Table of subscriptions expiring in less than 30 days';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            // Link::make('Website')
            //     ->href('http://orchid.software')
            //     ->icon('globe-alt'),

            // Link::make('Documentation')
            //     ->href('https://orchid.software/en/docs')
            //     ->icon('docs'),

            // Link::make('GitHub')
            //     ->href('https://github.com/orchidsoftware/platform')
            //     ->icon('social-github'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function query(): iterable
{
    $present = Carbon::now('Europe/Athens');
    $future = $present->copy()->add(30, 'day'); // 30 days from now
    
    return [
        'subscriptions' => Subscription::with('customer', 'service')
            ->whereBetween('expired_date', [$present, $future])
            ->orWhere('expired_date', '<', $present)
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

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */

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
