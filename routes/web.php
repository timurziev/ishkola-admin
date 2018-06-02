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


/**
 * Auth routes
 */
Route::group(['namespace' => 'Auth'], function () {

    // Authentication Routes...
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout')->name('logout');

    // Registration Routes...
    if (config('auth.users.registration')) {
        Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
        Route::post('register', 'RegisterController@register');
    }

    // Password Reset Routes...
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset');

    // Confirmation Routes...
    if (config('auth.users.confirm_email')) {
        Route::get('confirm/{user_by_code}', 'ConfirmController@confirm')->name('confirm');
        Route::get('confirm/resend/{user_by_email}', 'ConfirmController@sendEmail')->name('confirm.send');
    }

    // Social Authentication Routes...
    Route::get('social/redirect/{provider}', 'SocialLoginController@redirect')->name('social.redirect');
    Route::get('social/login/{provider}', 'SocialLoginController@login')->name('social.login');
});

/**
 * Backend routes
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => 'admin'], function () {

    // Dashboard
    Route::get('/', 'DashboardController@index')->name('dashboard');

    // Lessons
    Route::get('lessons', 'LessonController@index')->name('lessons');
    Route::get('lessons/create', 'LessonController@create')->name('lessons.create');
    Route::put('lessons/store', 'LessonController@store')->name('lessons.store');
    Route::get('lessons/{lesson}/edit', 'LessonController@edit')->name('lessons.edit');
    Route::put('lessons/{lesson}', 'LessonController@update')->name('lessons.update');

    // Groups
    Route::get('groups', 'GroupController@index')->name('groups');
    Route::get('groups/create', 'GroupController@create')->name('groups.create');
    Route::put('groups/store', 'GroupController@store')->name('groups.store');
    Route::get('groups/{group}/edit', 'GroupController@edit')->name('groups.edit');
    Route::put('groups/{group}', 'GroupController@update')->name('groups.update');
    Route::get('groups/{group}', 'GroupController@destroy')->name('groups.destroy');

    // Languages
    Route::get('langs', 'LangsController@index')->name('langs');
    Route::get('langs/create', 'LangsController@create')->name('langs.create');
    Route::put('langs/store', 'LangsController@store')->name('langs.store');
    Route::get('langs/{lang}/edit', 'LangsController@edit')->name('langs.edit');
    Route::put('langs/{lang}', 'LangsController@update')->name('langs.update');
    Route::get('langs/{group}', 'LangsController@destroy')->name('langs.destroy');

    // Users
    Route::get('users', 'UserController@index')->name('users');
    Route::get('roles/{role}', 'UserController@index')->name('roles');
    Route::get('users/{user}', 'UserController@show')->name('users.show');
    Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');
    Route::put('users/{user}', 'UserController@update')->name('users.update');
    Route::delete('users/{user}', 'UserController@destroy')->name('users.destroy');
    Route::get('permissions', 'PermissionController@index')->name('permissions');
    Route::get('permissions/{user}/repeat', 'PermissionController@repeat')->name('permissions.repeat');
    Route::get('dashboard/log-chart', 'DashboardController@getLogChartData')->name('dashboard.log.chart');
    Route::get('dashboard/registration-chart', 'DashboardController@getRegistrationChartData')->name('dashboard.registration.chart');
});


Route::get('/', 'HomeController@index');

/**
 * Membership
 */
Route::group(['as' => 'protection.'], function () {
    Route::get('membership', 'MembershipController@index')->name('membership')->middleware('protection:' . config('protection.membership.product_module_number') . ',protection.membership.failed');
    Route::get('membership/access-denied', 'MembershipController@failed')->name('membership.failed');
    Route::get('membership/clear-cache/', 'MembershipController@clearValidationCache')->name('membership.clear_validation_cache');
});
