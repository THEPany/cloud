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

// Marketing
Route::get('/', 'MarketingController')->name('marketing.index');

// Webhook
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
    Route::delete('organizations/{organization}', 'OrganizationController@destroy')->name('organizations.destroy');
    Route::put('organizations/{organization}/restore', 'OrganizationController@restore')->name('organizations.restore');
    Route::post('organizations/{organization}/{user}', 'OrganizationController@sendInvitationLink')->name('organizations.send.invitation');
    Route::get('organizations/{organization}/{user}', 'OrganizationController@invitation')->name('organizations.invitation')->middleware('signed');
    Route::delete('organizations/{organization}/{user}', 'OrganizationController@removeContributor')->name('organizations.remove.contributor');

    // Subscription
    Route::get('subscriptions', 'SubscriptionController@index')->name('subscriptions.index');
    Route::post('subscriptions', 'SubscriptionController@store')->name('subscriptions.store');
    Route::put('subscriptions', 'SubscriptionController@update')->name('subscriptions.update');
    Route::post('subscriptions/card', 'SubscriptionController@updateCard')->name('subscriptions.card');
    Route::get('subscriptions/cancel', 'SubscriptionController@cancelSubscription')->name('subscriptions.cancel');
    Route::post('subscriptions/resumeSubscription', 'SubscriptionController@resumeSubscription')->name('subscriptions.resumeSubscription');
    Route::post('subscriptions/cancelNowSubscription', 'SubscriptionController@cancelNowSubscription')->name('subscriptions.cancelNowSubscription');

    // Aplication
    Route::get('apps/{slug}', 'AppController@index')->name('apps.index');
    Route::get('apps/{slug}/collaborators', 'AppController@collaborator')->name('apps.collaborator');
    Route::get('apps/{slug}/collaborators/{user}', 'AppController@assignPermission')->name('apps.collaborator.permissions');
    Route::post('apps/{slug}/collaborators/{user}', 'AppController@permission')->name('apps.collaborator.permissions.store');
});

// Facturacion
Route::middleware(['auth', 'app.authorized'])->prefix('{slug}/invoice')->group(function () {
    Route::get('home','Invoice\HomeController@index')->name('invoice.home.index');

    // Article
    Route::get('articles', 'Invoice\ArticleController@index')->name('invoice.articles.index');
    Route::get('articles/create', 'Invoice\ArticleController@create')->name('invoice.articles.create');
    Route::post('articles', 'Invoice\ArticleController@store')->name('invoice.articles.store');
    Route::get('articles/{article}/edit', 'Invoice\ArticleController@edit')->name('invoice.articles.edit');
    Route::put('articles/{article}', 'Invoice\ArticleController@update')->name('invoice.articles.update');
    Route::delete('articles/{article}', 'Invoice\ArticleController@destroy')->name('invoice.articles.destroy');
    Route::put('articles/{article}/restore', 'Invoice\ArticleController@restore')->name('invoice.articles.restore');

    // Client
    Route::get('clients', 'Invoice\ClientController@index')->name('invoice.clients.index');
    Route::post('clients', 'Invoice\ClientController@store')->name('invoice.clients.store');
    Route::get('clients/create', 'Invoice\ClientController@create')->name('invoice.clients.create');
    Route::put('clients/{client}', 'Invoice\ClientController@update')->name('invoice.clients.update');
    Route::get('clients/{client}/edit', 'Invoice\ClientController@edit')->name('invoice.clients.edit');
    Route::delete('clients/{client}', 'Invoice\ClientController@destroy')->name('invoice.clients.destroy');
    Route::put('clients/{client}/restore', 'Invoice\ClientController@restore')->name('invoice.clients.restore');

    // Bill
    Route::get('bills', 'Invoice\BillController@index')->name('invoice.bills.index');
    Route::post('bills', 'Invoice\BillController@store')->name('invoice.bills.store');
    Route::get('bills/create', 'Invoice\BillController@create')->name('invoice.bills.create');
    Route::get('bills/{bill}', 'Invoice\BillController@show')->name('invoice.bills.show');
    Route::get('bills/{bill}/edit', 'Invoice\BillController@edit')->name('invoice.bills.edit');
    Route::put('bills/{bill}', 'Invoice\BillController@update')->name('invoice.bills.update');
    Route::get('bill/{bill}/preview', 'Invoice\BillController@preview')->name('invoice.bills.preview');

    // Pagos
    Route::get('payments', 'Invoice\PaymentController@index')->name('invoice.payments.index');
    Route::get('payments/create', 'Invoice\PaymentController@create')->name('invoice.payments.create');
    Route::post('payments', 'Invoice\PaymentController@store')->name('invoice.payments.store');
    Route::get('payments/{payment}', 'Invoice\PaymentController@show')->name('invoice.payments.show');
    Route::get('payments/{payment}/preview', 'Invoice\PaymentController@preview')->name('invoice.payments.preview');
});