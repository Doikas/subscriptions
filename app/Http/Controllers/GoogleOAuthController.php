<?php

namespace App\Http\Controllers;

use App\Services\GoogleOAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class GoogleOAuthController extends Controller
{
    protected $googleOAuth;

    public function __construct(GoogleOAuthService $googleOAuth)
    {
        $this->googleOAuth = $googleOAuth;
    }

    public function handleGoogleCallback(Request $request)
    {
        $code = $request->get('code');

        if (!$code) {
            return redirect($this->googleOAuth->getAuthorizationUrl());
        }

        $token = $this->googleOAuth->getAccessToken($code);
        $refreshToken = $token['refresh_token'];
        Log::info('Google Refresh Token: ' . $refreshToken);

        // Save the refresh token in the .env file
        $this->saveRefreshTokenToEnv($refreshToken);

        return redirect('/')->with('message', 'OAuth2 setup complete!');
    }

    private function saveRefreshTokenToEnv($refreshToken)
    {
        // Path to the .env file
        $envPath = base_path('.env');

        if (File::exists($envPath)) {
            // Get current content of .env
            $envContent = File::get($envPath);

            // Check if the GOOGLE_REFRESH_TOKEN exists and replace it
            if (strpos($envContent, 'GOOGLE_REFRESH_TOKEN=') !== false) {
                // Replace the existing refresh token
                $newContent = preg_replace(
                    '/GOOGLE_REFRESH_TOKEN=.*/',
                    'GOOGLE_REFRESH_TOKEN=' . $refreshToken,
                    $envContent
                );
            } else {
                // Add the refresh token if it doesn't exist
                $newContent = $envContent . "\nGOOGLE_REFRESH_TOKEN=" . $refreshToken;
            }

            // Save the new .env content
            File::put($envPath, $newContent);
        }
    }
}
