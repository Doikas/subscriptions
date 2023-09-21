<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\ExpirationReminder30Days;
use App\Mail\ExpirationReminder15Days;
use App\Mail\ExpirationReminder5Days;
use App\Mail\ExpirationReminder0Days;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;


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
        // Ensure $subscription->expired_date is a Carbon date object
        $expiredDate = Carbon::parse($subscription->expired_date);

        $daysUntilExpiration = $present->diffInDays($expiredDate);

        if ($daysUntilExpiration === 30) {
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
        }elseif ($daysUntilExpiration === 15) {
            $data = [
                'customer.email' => $subscription->customer->email,
                'customer.pronunciation' => $subscription->customer->pronunciation,
                'service.name' => $subscription->service->name,
                'domain' => $subscription->domain,
                'expired_date' => $expiredDate->formatLocalized('%d-%m-%Y'),
            ];

            Mail::to($subscription->customer->email)
            ->cc('alexakis@wdesign.gr')
            ->send(new ExpirationReminder15Days($data));
        }elseif ($daysUntilExpiration === 5) {
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
        }elseif ($daysUntilExpiration === 0) {
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
        }
    }
 }
}