<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport; // Ensure you have this import
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Address;

class GoogleOAuthService
{
    protected $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->client->setAccessType('offline');  // Ensures refresh tokens are provided
        $this->client->setPrompt('consent');  // Forces user to consent each time

        // Add the required Google OAuth2 scopes
        $this->client->addScope('https://mail.google.com');
    }

    public function getClient()
    {
        return $this->client;
    }

    /**
     * Generates the authorization URL for the user to give consent.
     */
    public function getAuthorizationUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Fetches the access token using the authorization code provided by Google.
     *
     * @param string $code Authorization code from Google
     * @return array The access token response
     */
    public function getAccessToken($code)
    {
        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    /**
     * Refreshes the access token using the refresh token.
     *
     * @param string $refreshToken The refresh token
     * @return array The refreshed access token response
     */
    public function refreshAccessToken($refreshToken)
    {
        return $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
    }
	
	 public function sendEmailWithOAuth2($to, $subject, $body)
    {
        // Refresh the access token
        $refreshToken = env('GOOGLE_REFRESH_TOKEN');
        $token = $this->refreshAccessToken($refreshToken);

        if (isset($token['error'])) {
            Log::error('Error refreshing access token: ' . json_encode($token));
            return false;
        }

        $accessToken = $token['access_token'];

        // Configure Symfony Mailer with OAuth2
        $transport = new EsmtpTransport('smtp.gmail.com', 465, true);
        $transport->setUsername(env('MAIL_USERNAME')); // Your Gmail address
        $transport->setPassword($accessToken); // Use the OAuth2 access token as the password

        $mailer = new Mailer($transport);

        // Create and send the email
        $email = (new Email())
            ->from(env('MAIL_FROM_ADDRESS')) // From address from .env
            ->to($to)
            ->subject($subject)
            ->text($body);

        try {
            $mailer->send($email);
            return true; // Email sent successfully
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
            Log::error('Email details: ' . json_encode([
                'to' => $to,
                'subject' => $subject,
                'body' => $body,
                'error' => $e->getMessage()
            ]));
            return false; // Email not sent
        }
    }

}
