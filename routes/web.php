<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Services\GoogleOAuthService;
use App\Http\Controllers\GoogleOAuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/expiration_reminder', function () {
    $subscription = App\Models\Subscription::find(1)->get();
    Artisan::call('schedule:run');
    
    return new App\Mail\ExpirationReminder($subscription);
});

Route::get('/oauth2callback', [GoogleOAuthController::class, 'handleGoogleCallback'])->name('oauth2.callback');

Route::get('/auth/google', function () {
    $googleOAuthService = new \App\Services\GoogleOAuthService();
    return redirect($googleOAuthService->getAuthorizationUrl());
})->name('oauth2.google');
