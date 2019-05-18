<?php

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

Route::domain(env('APP_DOMAIN'))->group(function () {
    // Auth
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login')->name('login.attempt');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register')->name('register.attempt');
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

    Route::get('/', 'MarketingController')->name('marketing.index');

    Route::post(
        'braintree/webhook',
        '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook'
    );

    Route::middleware(['auth'])->group(function () {

        Route::get('home', 'HomeController@index')->name('home.index');

        // Organization
        Route::get('organizations', 'OrganizationController@index')->name('organizations.index');
        Route::get('organizations/create', 'OrganizationController@create')->name('organizations.create');
        Route::post('organizations', 'OrganizationController@store')->name('organizations.store');
        Route::get('organizations/{organization}/edit', 'OrganizationController@edit')->name('organizations.edit');
        Route::put('organizations/{organization}', 'OrganizationController@update')->name('organizations.update');
        Route::post('organizations/{organization}/{user}', 'OrganizationController@sendInvitationLink')->name('organizations.send.invitation');
        Route::get('organizations/{organization}/{user}', 'OrganizationController@invitation')->name('organizations.invitation')->middleware('signed');

        // Subscription
        Route::get('subscriptions', 'SubscriptionController@index')->name('subscriptions.index');
        Route::post('subscriptions', 'SubscriptionController@store')->name('subscriptions.store');
        Route::put('subscriptions', 'SubscriptionController@update')->name('subscriptions.update');
        Route::post('subscriptions/card', 'SubscriptionController@updateCard')->name('subscriptions.card');
        Route::get('subscriptions/cancel', 'SubscriptionController@cancelSubscription')->name('subscriptions.cancel');
        Route::post('subscriptions/resumeSubscription', 'SubscriptionController@resumeSubscription')->name('subscriptions.resumeSubscription');
        Route::post('subscriptions/cancelNowSubscription', 'SubscriptionController@cancelNowSubscription')->name('subscriptions.cancelNowSubscription');
    });
});

Route::domain('{organization}.'.env('APP_DOMAIN'))->group(function () {
    Route::get('apps', 'AppController@index')->name('apps.index');
    Route::get('apps/other', 'AppController@other')->name('apps.other');
});