<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use App\Jobs\SendEmailJob;


class SubscriptionExpirationReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:subscription-expiration-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send expiration reminders for subscriptions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Initialize GoogleOAuthService to handle OAuth2
        $googleOAuthService = app(\App\Services\GoogleOAuthService::class);

        // Get the Google client and refresh the access token
        $googleClient = $googleOAuthService->getClient();
        $accessToken = $googleClient->fetchAccessTokenWithRefreshToken(env('GOOGLE_REFRESH_TOKEN'));

        // Set up the mailer with OAuth2 transport
        $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
            'smtp.gmail.com', 587, false
        );
        $transport->setUsername(env('MAIL_USERNAME'));
        $transport->setPassword($accessToken['access_token']);
        $mailer = new \Symfony\Component\Mailer\Mailer($transport);

        $subscriptions = Subscription::with('customer', 'service')->get();
        $present = Carbon::now('Europe/Athens');

        foreach ($subscriptions as $subscription) {
            $ccEmail = env('CC_EMAIL');

            if ($subscription->last_email_automation_sent_at !== null) {
                $last_email_automation_sent_at = Carbon::parse($subscription->last_email_automation_sent_at);
                if ($last_email_automation_sent_at->isToday()) {
                    continue;
                }
            }

            $expiredDate = Carbon::parse($subscription->expired_date);
            if ($expiredDate->isFuture() || $expiredDate->isToday()) {
                $daysUntilExpiration = $present->startOfDay()->diffInDays($expiredDate->startOfDay());

                $subject = '';
                $content = '';

                if ($daysUntilExpiration === 30 || $daysUntilExpiration === 15) {
                    $data = $this->prepareEmailData($subscription, $expiredDate);
                    $emailView = 'email.expiration_reminder30days';
                    $subject = $this->getEmailSubject($emailView, $subscription->domain);
                    $content = view($emailView, $data)->render();
                } elseif ($daysUntilExpiration === 5) {
                    $data = $this->prepareEmailData($subscription, $expiredDate);
                    $emailView = 'email.expiration_reminder5days';
                    $subject = $this->getEmailSubject($emailView, $subscription->domain);
                    $content = view($emailView, $data)->render();
                } elseif ($daysUntilExpiration === 0) {
                    $data = $this->prepareEmailData($subscription, $expiredDate);
                    $emailView = 'email.expiration_reminder0days';
                    $subject = $this->getEmailSubject($emailView, $subscription->domain);
                    $content = view($emailView, $data)->render();
                }

                if (!empty($subject) && !empty($content)) {
                    // Dispatch the email job to the queue instead of sending directly
                    try {
                        SendEmailJob::dispatch($subscription, $ccEmail, $subject, $content);

                        $subscription->update(['last_email_automation_sent_at' => now()]);

                        // Optional: log the job dispatching for easier debugging
                        $this->insertEmailLog($subscription->id, $subject, $content, true);
                    } catch (\Exception $e) {
                        Log::error('Auto email sending failed: ' . $e->getMessage());
                    }
                }
            }
        }
    }

    private function prepareEmailData($subscription, $expiredDate)
    {
        return [
            'customer_pronunciation' => $subscription->customer->pronunciation,
            'service_name' => $subscription->service->name,
            'domain' => $subscription->domain,
            'price' => $subscription->price,
            'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
            'content' => '',
        ];
    }

    private function getEmailSubject($view, $domain)
    {
        $functionHelper = new \App\FunctionHelper();
        return $functionHelper->getEmailSubject($view, $domain);
    }

    private function insertEmailLog($subscriptionId, $subject, $content, $sentSuccessfully)
    {
        $emailLog = new \App\Models\EmailLog();
        $emailLog->subscription_id = $subscriptionId;
        $emailLog->subject = $subject;
        $emailLog->content = $content;
        $emailLog->sent_successfully = $sentSuccessfully;
        $emailLog->sent_at = now();
        $emailLog->save();
    }
}
