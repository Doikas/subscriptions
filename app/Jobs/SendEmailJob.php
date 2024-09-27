<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subscription;
    public $ccEmail;
    public $subject;
    public $content;

    /**
     * Create a new job instance.
     *
     * @param $subscription
     * @param $ccEmail
     * @param $subject
     * @param $content
     */
    public function __construct($subscription, $ccEmail, $subject, $content)
    {
        $this->subscription = $subscription;
        $this->ccEmail = $ccEmail;
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $email = (new Email())
                ->from(new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')))
                ->to($this->subscription->customer->email)
                ->cc($this->ccEmail)
                ->subject($this->subject)
                ->html($this->content);

            $mailer = app(Mailer::class);
            $mailer->send($email);

            Log::info('Email sent successfully to ' . $this->subscription->customer->email);
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
        }
    }
}
