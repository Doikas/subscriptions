<?php

/**
 * Putting this here to help remind you where this came from.
 *
 * I'll get back to improving this and adding more as time permits
 * if you need some help feel free to drop me a line.
 *
 * * Twenty-Years Experience
 * * PHP, JavaScript, Laravel, MySQL, Java, Python and so many more!
 *
 *
 * @author  Simple-Pleb <plebeian.tribune@protonmail.com>
 * @website https://www.simple-pleb.com
 * @source https://github.com/simplepleb/laravel-email-templates
 *
 * @license Free to do as you please
 *
 * @since 1.0
 *
 */

 namespace App\Mail;

 use App\Models\Subscription;
 use Illuminate\Bus\Queueable;
 use Illuminate\Contracts\Queue\ShouldQueue;
 use Illuminate\Mail\Mailable;
 use Illuminate\Queue\SerializesModels;
 use Illuminate\Mail\Mailables\Content;
 use Illuminate\Mail\Mailables\Envelope;
 use Illuminate\Support\Facades\Log;
 
 class SubscriptionStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @param array $data
     * @param string $subject
     * @param string $content
     * @return void
     */
    public function __construct($data, $subject, $content)
    {
        $this->data = $data;
        $this->subject($subject);
        
    }

    public function build()
    {
        return $this
            ->view('email.subscription_statusnotification')
            ->with([
                'customer_pronunciation' => $this->data['customer_pronunciation'],
                'service_name' => $this->data['service_name'],
                'domain' => $this->data['domain'],
                'expired_date' => $this->data['expired_date'],
                'content' => $this->data['content'], // Add 'content' if you need it
            ]);
    }
    public function attachments()
    {
        return [];
    }
}
