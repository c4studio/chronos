<?php

/* AUTH */
Route::get('login', ['as' => 'chronos.auth.login', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('login', ['as' => 'chronos.auth.login_post', 'uses' => 'Auth\LoginController@login']);
Route::get('logout', ['as' => 'chronos.auth.logout', 'uses' => 'Auth\LoginController@logout']);
Route::get('password/reset', ['as' => 'chronos.auth.password_reset_request', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
Route::post('password/email', ['as' => 'chronos.auth.password_reset_request_post', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
Route::get('password/reset/success', ['as' => 'chronos.auth.password_reset_success', 'uses' => 'Auth\ResetPasswordController@showResetSuccess']);
Route::get('password/reset/{token}', ['as' => 'chronos.auth.password_reset_form', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
Route::post('password/reset', ['as' => 'chronos.auth.password_reset_form_post', 'uses' => 'Auth\ResetPasswordController@reset']);

Route::group(['middleware' => 'auth'], function () {
    /* AUTH */
    Route::get('profile', ['as' => 'chronos.auth.profile', 'uses' => 'Auth\ProfileController@edit']);
    Route::post('profile', ['as' => 'chronos.auth.profile_post', 'uses' => 'Auth\ProfileController@update']);
    Route::post('profile/password', ['as' => 'chronos.auth.update_password', 'uses' => 'Auth\ProfileController@update_password']);
    Route::post('profile/picture', ['as' => 'chronos.auth.update_picture', 'uses' => 'Auth\ProfileController@update_picture']);

    /* DASHBOARD */
    Route::get('/', [
        'as' => 'chronos.dashboard',
        'uses' => 'DashboardController@index',
        'middleware' => 'can:view_dashboard'
    ]);

    /* PERMISSIONS */
    Route::get('users/permissions', ['as' => 'chronos.users.permissions', 'uses' => 'Users\RolesController@permissions', 'middleware' => 'can:edit_permissions']);

    /* ROLES */
    Route::get('users/roles', ['as' => 'chronos.users.roles', 'uses' => 'Users\RolesController@index', 'middleware' => 'can:view_roles']);

    /* SETTINGS */
    Route::get('settings/access-tokens', ['as' => 'chronos.settings.access_tokens', 'uses' => 'Settings\AccessTokensController@index', 'middleware' => 'can:edit_access_tokens']);

    Route::get('settings/image-styles', ['as' => 'chronos.settings.image_styles', 'uses' => 'Settings\ImageStylesController@index', 'middleware' => 'can:view_image_styles']);
    Route::get('settings/image-styles/create', ['as' => 'chronos.settings.image_styles.create', 'uses' => 'Settings\ImageStylesController@create', 'middleware' => 'can:edit_image_styles']);
    Route::get('settings/image-styles/{style}/edit', ['as' => 'chronos.settings.image_styles.edit', 'uses' => 'Settings\ImageStylesController@edit', 'middleware' => 'can:edit_image_styles']);
});
