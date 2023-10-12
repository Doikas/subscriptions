<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\ExpirationReminder30Days;
use App\Mail\ExpirationReminder5Days;
use App\Mail\ExpirationReminder0Days;
use App\Models\Subscription;
use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


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
            $emailSentSuccessfully = false;
    
            if ($expiredDate->isFuture() || $expiredDate->isToday()) {
                $daysUntilExpiration = $present->startOfDay()->diffInDays(($expiredDate)->startOfDay());
                
                $subject = '';
                $content = '';
    
                if ($daysUntilExpiration === 30 || $daysUntilExpiration === 15) {
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
                } elseif ($daysUntilExpiration === 5) {
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
                } elseif ($daysUntilExpiration === 0) {
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
                    $subscription->update(['last_email_automation_sent_at' => now()]);
                    $this->insertEmailLog($subscription->id, $subject, $content, $emailSentSuccessfully);
                    
                }
            }
        }
    }
    
    
    /**
     * Insert a record into the email logs table with the subscription_id.
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