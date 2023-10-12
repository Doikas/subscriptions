<?php

namespace App\Orchid\Screens\Subscriptions;

use Orchid\Screen\Screen;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayout;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayoutTable;
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
use Illuminate\Support\Facades\DB;
use App\Models\EmailLog;


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
                ->filters(SubscriptionFiltersLayoutTable::class)
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
    $emailSentSuccessfully = false;
    $ccEmail = env('CC_EMAIL');

    if ($expiredDate->isFuture()) {
        $daysUntilExpiration = $present->startOfDay()->diffInDays(($expiredDate)->startOfDay());

        $subject = '';
        $content = '';

        if ($daysUntilExpiration > 30) {
            $data = [
                'customer_pronunciation' => $subscription->customer->pronunciation,
                'service_name' => $subscription->service->name,
                'domain' => $subscription->domain,
                'price' => $subscription->price,
                'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
                'content' => $content,
            ];
            $functionHelper = new \App\FunctionHelper();
            $emailView = 'email.subscription_statusnotification'; // Set the email view here
            $subject = $functionHelper->getEmailSubject($emailView, $subscription->domain);
            $content = view('email.subscription_statusnotification', $data)->render();
            $mailable = new \App\Mail\SubscriptionStatusNotification($data, $subject, $content);
        } elseif ($daysUntilExpiration <= 30 && $daysUntilExpiration > 5) {
            $data = [
                'customer_pronunciation' => $subscription->customer->pronunciation,
                'service_name' => $subscription->service->name,
                'domain' => $subscription->domain,
                'price' => $subscription->price,
                'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
                'content' => $content,
            ];
            $functionHelper = new \App\FunctionHelper();
            $emailView = 'email.expiration_reminder30days'; // Set the email view here
            $subject = $functionHelper->getEmailSubject($emailView, $subscription->domain);
            $content = view('email.expiration_reminder30days', $data)->render();
            $mailable = new \App\Mail\ExpirationReminder30Days($data, $subject, $content);
        } elseif ($daysUntilExpiration <= 5 && $daysUntilExpiration > 0) {
            $data = [
                'customer_pronunciation' => $subscription->customer->pronunciation,
                'service_name' => $subscription->service->name,
                'domain' => $subscription->domain,
                'price' => $subscription->price,
                'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
                'content' => $content,
            ];
            $functionHelper = new \App\FunctionHelper();
            $emailView = 'email.expiration_reminder5days'; // Set the email view here
            $subject = $functionHelper->getEmailSubject($emailView, $subscription->domain);
            $content = view('email.expiration_reminder5days', $data)->render();
            $mailable = new \App\Mail\ExpirationReminder5Days($data, $subject, $content);
        } elseif ($daysUntilExpiration == 0 || $daysUntilExpiration < 0) {
            
            $data = [
                'customer_pronunciation' => $subscription->customer->pronunciation,
                'service_name' => $subscription->service->name,
                'domain' => $subscription->domain,
                'price' => $subscription->price,
                'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
                'content' => $content,
            ];
            $functionHelper = new \App\FunctionHelper();
            $emailView = 'email.expiration_reminder0days'; // Set the email view here
            $subject = $functionHelper->getEmailSubject($emailView, $subscription->domain);
            $content = view('email.expiration_reminder0days', $data)->render();
            $mailable = new \App\Mail\ExpirationReminder0Days($data, $subject, $content);
        }

        if (!empty($subject) && !empty($content)) {
            // Send the email
            $sentMessage = Mail::to($subscription->customer->email)
            ->cc($ccEmail)
            ->send($mailable);
            
            
            $emailSentSuccessfully = $sentMessage !== null;

            // Insert a record into the email logs table
            $this->insertEmailLog($subscription->id, $subject, $content, $emailSentSuccessfully);
            if ($emailSentSuccessfully) {
                Toast::info(__('Status email sent successfully'), 'success');
            }
            
        }
    }else {
        $subject = '';
        $content = '';
        $data = [
            'customer_pronunciation' => $subscription->customer->pronunciation,
            'service_name' => $subscription->service->name,
            'domain' => $subscription->domain,
            'price' => $subscription->price,
            'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
            'content' => $content,
        ];
        $functionHelper = new \App\FunctionHelper();
        $emailView = 'email.expiration_reminder0days'; // Set the email view here
        $subject = $functionHelper->getEmailSubject($emailView, $subscription->domain);
        $content = view('email.expiration_reminder0days', $data)->render();
        $mailable = new \App\Mail\ExpirationReminder0Days($data, $subject, $content);

        if (!empty($subject) && !empty($content)) {
            // Send the email
            $sentMessage = Mail::to($subscription->customer->email)
            ->cc($ccEmail)
            ->send($mailable);
            
            
            $emailSentSuccessfully = $sentMessage !== null;

            // Insert a record into the email logs table
            $this->insertEmailLog($subscription->id, $subject, $content, $emailSentSuccessfully);
            if ($emailSentSuccessfully) {
                Toast::info(__('Status email sent successfully'), 'success');
            }
            
        }

    }

    return back();
}


/**
 * Insert a record into the email logs table.
 */
private function insertEmailLog($subscriptionId, $subject, $content, $sentSuccessfully)
{
    $emailLog = new EmailLog();
    $emailLog->subscription_id = $subscriptionId; // Associate the email log with the subscription
    $emailLog->subject = $subject; // Replace with the actual subject
    $emailLog->content = $content; // Replace with the actual content
    $emailLog->sent_successfully = $sentSuccessfully;
    $emailLog->sent_at = now(); // You can add a timestamp for when the email was sent
    $emailLog->save();
}
    
}
