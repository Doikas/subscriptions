<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Illuminate\Support\Facades\Mail;
use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Set up Google Client
        $googleClient = new GoogleClient();
        $googleClient->setClientId(env('GOOGLE_CLIENT_ID'));
        $googleClient->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $googleClient->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $googleClient->setAccessType('offline');
        $googleClient->setPrompt('consent');

        // Fetch the access token using the refresh token
        $accessToken = $googleClient->fetchAccessTokenWithRefreshToken(env('GOOGLE_REFRESH_TOKEN'));

        // Create an SMTP transport using Symfony's Mailer
        $dsn = sprintf(
            'smtp://%s@%s?smtp_auth=xoauth2&access_token=%s&encryption=tls',
            urlencode(env('MAIL_USERNAME')),
            'smtp.gmail.com:587',
            urlencode($accessToken['access_token'])
        );
        
        $transport = Transport::fromDsn($dsn);

        // Set the mailer in Laravel
        $mailer = new Mailer($transport);
        Mail::mailer('smtp')->setSymfonyTransport($transport);
    }
}
