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

class ExpirationReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $options;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private $data)
    {
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Service Expiration Reminder',
        );
    }

    public function content()
    {
        //dd($this->data);
        return new Content(

            view: 'email.expiration_reminder',
            with: [
                'customer.pronunciation' => $this->data['customer_pronunciation'],
                'service.name' => $this->data['service_name'],
                'expired_date' => $this->data['expired_date'],
            ],
            
        );
    }

    public function attachments()
    {
        return [];
    }
    // public function build()
    // {
    //     return $this->view('email.expiration_reminder');
    // }


}
