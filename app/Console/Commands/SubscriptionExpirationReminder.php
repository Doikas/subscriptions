<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\ExpirationReminder;
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
    protected $signature = 'auto:SubscriptionExpirationReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */

     public function __construct()
    {
        parent::__construct();
    }  
    public function handle()
    {
        
        $subscription = Subscription::joinRelationship('customers','services');

        foreach($subscription as $sub){
            $present = Carbon::now('Europe/Athens');
            $plupres = $present->add(30, 'day');

            if ($sub->expired_date == $plupres)
            {
                //var_dump($s);
                echo "30 days expiration";  
                $data = [
                    'customer.email' => $sub->customer_email,
                    'customer.pronunciation' => $sub->customer_pronunciation,
                    'service.name'=>$sub->service_name,
                    'expired_date'=> $sub->expired_date
                ];
                Mail::to($sub->customer_email)->send(new ExpirationReminder($data));


            }elseif ($sub->expired_date == $plupres){
                echo "15 days expiration";
                $data = [
                    'customer.email' => $sub->customer_email,
                    'customer.pronunciation' => $sub->customer_pronunciation,
                    'service.name'=>$sub->service_name,
                    'expired_date'=> $sub->expired_date
                ];
                Mail::to($sub->customer_email)->send(new ExpirationReminder($data));
            }elseif ($sub->expiration == $plupres){
                echo "5 days expiration";
                $data = [
                    'customer.email' => $sub->customer_email,
                    'customer.pronunciation' => $sub->customer_pronunciation,
                    'service.name'=>$sub->service_name,
                    'expired_date'=> $sub->expired_date
                ];
                Mail::to($sub->customer_email)->send(new ExpirationReminder($data));
            }elseif ($sub->expiration == $plupres){
                echo "today expiration";
                $data = [
                    'customer.email' => $sub->customer_email,
                    'customer.pronunciation' => $sub->customer_pronunciation,
                    'service.name'=>$sub->service_name,
                    'expired_date'=> $sub->expired_date
                ];
                Mail::to($sub->customer_email)->send(new ExpirationReminder($data));
            }

        }
        //var_dump($services);

       echo date("Y-m-d", strtotime("-30 days")) ;
       //2022-12-25
    }
}