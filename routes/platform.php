<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;
use App\Orchid\Screens\Customer\CustomerListScreen;
use App\Orchid\Screens\Customer\CustomerEditScreen;
use App\Orchid\Screens\Services\ServiceListScreen;
use App\Orchid\Screens\Services\ServiceEditScreen;
use App\Orchid\Screens\Subscriptions\SubscriptionListScreen;
use App\Orchid\Screens\Subscriptions\SubscriptionEditScreen;
use App\Http\Controllers\ServiceController;
use App\Orchid\Screens\EmailLog\EmailLogListScreen;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push(__('User'), route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users > User
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Role'), route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example screen'));

Route::screen('example-fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('example-layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('example-charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('example-editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('example-cards', ExampleCardsScreen::class)->name('platform.example.cards');
Route::screen('example-advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');

//Route::screen('idea', Idea::class, 'platform.screens.idea');


// Route::screen('customers', CustomerScreen::class)->name('platform.customers');
Route::screen('customers', CustomerListScreen::class)
    ->name('platform.systems.customers')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Customers'), route('platform.systems.customers')));
        
Route::screen('services', ServiceListScreen::class)
    ->name('platform.systems.services')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Services'), route('platform.systems.services')));

Route::screen('subscriptions', SubscriptionListScreen::class)
    ->name('platform.systems.subscriptions')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Subscriptions'), route('platform.systems.subscriptions')));

// Platform > System > Customers > Edit
Route::screen('customers/{customer}/edit', CustomerEditScreen::class)
    ->name('platform.systems.customers.edit')
    ->breadcrumbs(fn (Trail $trail, $customer) => $trail
        ->parent('platform.systems.customers')
        ->push(__('Customer'), route('platform.systems.customers.edit', $customer)));

// Platform > System > Customers > Create
Route::screen('customers/create', CustomerEditScreen::class)
    ->name('platform.systems.customers.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.customers')
        ->push(__('Create'), route('platform.systems.customers.create')));

// Platform > System > Services > Edit
Route::screen('services/{service}/edit', ServiceEditScreen::class)
->name('platform.systems.services.edit')
->breadcrumbs(fn (Trail $trail, $service) => $trail
    ->parent('platform.systems.services')
    ->push(__('Service'), route('platform.systems.services.edit', $service)));

// Platform > System > Services > Create
Route::screen('services/create', ServiceEditScreen::class)
->name('platform.systems.services.create')
->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.systems.services')
    ->push(__('Create'), route('platform.systems.services.create')));

    // Platform > System > Subscription > Edit
Route::screen('subscriptions/{subscription}/edit', SubscriptionEditScreen::class)
->name('platform.systems.subscriptions.edit')
->breadcrumbs(fn (Trail $trail, $subscription) => $trail
    ->parent('platform.systems.subscriptions')
    ->push(__('Subscription'), route('platform.systems.subscriptions.edit', $subscription)));

// Platform > System > Subscription > Create
Route::screen('subscriptions/create', SubscriptionEditScreen::class)
->name('platform.systems.subscriptions.create')
->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.systems.subscriptions')
    ->push(__('Create'), route('platform.systems.subscriptions.create')));

Route::get('subscriptions/get-expiration/{serviceId}', [ServiceController::class, 'getExpiration'])
    ->name('platform.service.getExpiration');

Route::post('/send-status-email/{id}', [SubscriptionListScreen::class, 'sendStatusEmail'])
    ->name('platform.subscriptions.sendStatusEmail');

Route::screen('emaillogs', EmailLogListScreen::class)
    ->name('platform.systems.emailogs')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Email Logs'), route('platform.systems.emailogs')));

            