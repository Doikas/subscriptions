<?php

use Illuminate\Support\Facades\Route;


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

    $subscription = App\Models\Subscription::find(1);
    $customer_name = 'subscription.customer_id';
    $options = array(
        'invoice_id' => '10087866','customer_name'=> $customer_name,  'invoice_total' => '100.07', 'download_link' => 'http://gotohere.com',
    );
    return new App\Mail\ExpirationReminder($subscription, $options);
});