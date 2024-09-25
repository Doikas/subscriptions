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
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SubscriptionStatusNotification;
use App\Mail\ExpirationReminder30Days;
use App\Mail\ExpirationReminder5Days;
use App\Mail\ExpirationReminder0Days;
use App\Models\EmailLog;
use Symfony\Component\Mime\Address;

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
            'subscriptions' => Subscription::with('customer', 'service')
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
        Toast::info(__('Subscription was removed.'));
    }

    public function sendStatusEmail($id)
{
    // Retrieve the subscription by ID
    $subscription = Subscription::findOrFail($id);
    $expiredDate = Carbon::parse($subscription->expired_date);
    $present = Carbon::now('Europe/Athens');
    $emailSentSuccessfully = false;
    $ccEmail = env('CC_EMAIL');

    // Initialize GoogleOAuthService to handle OAuth2
    $googleOAuthService = app(\App\Services\GoogleOAuthService::class);

    // Get the Google client and refresh the access token
    $googleClient = $googleOAuthService->getClient();
    $accessToken = $googleClient->fetchAccessTokenWithRefreshToken(env('GOOGLE_REFRESH_TOKEN'));

    // Set up the mailer with OAuth2 transport
    $email = new \Symfony\Component\Mime\Email();
    $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
        'smtp.gmail.com', 587, false
    );
    $transport->setUsername(env('MAIL_USERNAME'));
    $transport->setPassword($accessToken['access_token']);
    $mailer = new \Symfony\Component\Mailer\Mailer($transport);

    if ($expiredDate->isFuture()) {
        $daysUntilExpiration = $present->startOfDay()->diffInDays($expiredDate->startOfDay());

        $subject = '';
        $content = '';

        // Define the data for the email
        $data = [
            'customer_pronunciation' => $subscription->customer->pronunciation,
            'service_name' => $subscription->service->name,
            'domain' => $subscription->domain,
            'price' => $subscription->price,
            'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
            'content' => $content,
        ];

        // Define email based on expiration period
        if ($daysUntilExpiration > 30) {
            $emailView = 'email.subscription_statusnotification'; 
            $functionHelper = new \App\FunctionHelper();
            $subject = $functionHelper->getEmailSubject($emailView, $subscription->domain);
            $content = view('email.subscription_statusnotification', $data)->render();
            $mailable = new SubscriptionStatusNotification($data, $subject, $content);
        } elseif ($daysUntilExpiration <= 30 && $daysUntilExpiration > 5) {
            $emailView = 'email.expiration_reminder30days';
            $functionHelper = new \App\FunctionHelper();
            $subject = $functionHelper->getEmailSubject($emailView, $subscription->domain);
            $content = view('email.expiration_reminder30days', $data)->render();
            $mailable = new ExpirationReminder30Days($data, $subject, $content);
        } elseif ($daysUntilExpiration <= 5 && $daysUntilExpiration > 0) {
            $emailView = 'email.expiration_reminder5days';
            $functionHelper = new \App\FunctionHelper();
            $subject = $functionHelper->getEmailSubject($emailView, $subscription->domain);
            $content = view('email.expiration_reminder5days', $data)->render();
            $mailable = new ExpirationReminder5Days($data, $subject, $content);
        } elseif ($daysUntilExpiration == 0 || $daysUntilExpiration < 0) {
            $emailView = 'email.expiration_reminder0days';
            $functionHelper = new \App\FunctionHelper();
            $subject = $functionHelper->getEmailSubject($emailView, $subscription->domain);
            $content = view('email.expiration_reminder0days', $data)->render();
            $mailable = new ExpirationReminder0Days($data, $subject, $content);
        }

        if (!empty($subject) && !empty($content)) {
            // Send the email using the custom mailer transport
            try {
                $email = (new \Symfony\Component\Mime\Email())
                    ->from(new \Symfony\Component\Mime\Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')))
                    ->to($subscription->customer->email)
                    ->cc($ccEmail)
                    ->subject($subject)
                    ->html($content);

                $mailer->send($email);

                $emailSentSuccessfully = true;

                // Insert a record into the email logs table
                $this->insertEmailLog($subscription->id, $subject, $content, $emailSentSuccessfully);
                Toast::info(__('Status email sent successfully.'));
            } catch (\Exception $e) {
                Log::error('Email sending failed: ' . $e->getMessage());
                Toast::error(__('Failed to send status email.'));
            }
        }
    } else {
        // Handle cases where the subscription has expired
        $data = [
            'customer_pronunciation' => $subscription->customer->pronunciation,
            'service_name' => $subscription->service->name,
            'domain' => $subscription->domain,
            'price' => $subscription->price,
            'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
            'content' => $content,
        ];
        $emailView = 'email.expiration_reminder0days'; 
        $functionHelper = new \App\FunctionHelper();
        $subject = $functionHelper->getEmailSubject($emailView, $subscription->domain);
        $content = view('email.expiration_reminder0days', $data)->render();
        $mailable = new ExpirationReminder0Days($data, $subject, $content);

        try {
            $email = (new \Symfony\Component\Mime\Email())
                ->from(new \Symfony\Component\Mime\Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')))
                ->to($subscription->customer->email)
                ->cc($ccEmail)
                ->subject($subject)
                ->html($content);

            $mailer->send($email);

            $emailSentSuccessfully = true;

            // Insert a record into the email logs table
            $this->insertEmailLog($subscription->id, $subject, $content, $emailSentSuccessfully);
            Toast::info(__('Status email sent successfully.'));
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            Toast::error(__('Failed to send status email.'));
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
        $emailLog->subject = $subject;
        $emailLog->content = $content;
        $emailLog->sent_successfully = $sentSuccessfully;
        $emailLog->sent_at = now(); // Timestamp for when the email was sent
        $emailLog->save();
    }
}
