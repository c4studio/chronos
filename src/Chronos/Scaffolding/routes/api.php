<?php

/* PERMISSIONS */
Route::patch('users/permissions', ['as' => 'api.users.permissions.update', 'uses' => 'Users\RolesController@permissions_update']);

/* ROLES */
Route::get('users/roles', ['uses' => 'Users\RolesController@index', 'as' => 'api.users.roles']);
Route::post('users/roles', ['uses' => 'Users\RolesController@store', 'as' => 'api.users.roles.store']);
Route::get('users/roles/{role}/users', ['uses' => 'Users\RolesController@users','as' => 'api.users.roles.users']);
Route::patch('users/roles/{role}', ['uses' => 'Users\RolesController@update','as' => 'api.users.roles.update']);
Route::delete('users/roles/{role}', ['uses' => 'Users\RolesController@destroy','as' => 'api.users.roles.destroy']);

/* SETTINGS */
Route::get('settings/access-tokens', ['uses' => 'Settings\AccessTokensController@index', 'as' => 'api.settings.access_tokens']);
Route::delete('settings/access-tokens/{token}', ['uses' => 'Settings\AccessTokensController@destroy', 'as' => 'api.settings.access_tokens.destroy']);
Route::post('settings/access-tokens', ['uses' => 'Settings\AccessTokensController@store', 'as' => 'api.settings.access_tokens.store', ]);

Route::get('settings/image-styles', ['uses' => 'Settings\ImageStylesController@index', 'as' => 'api.settings.image_styles']);
Route::delete('settings/image-styles/{style}', ['uses' => 'Settings\ImageStylesController@destroy', 'as' => 'api.settings.image_styles.destroy']);
Route::delete('settings/image-styles/destroy_styles/{style}', ['uses' => 'Settings\ImageStylesController@destroy_styles', 'as' => 'api.settings.image_styles.destroy_styles']);
Route::get('settings/image-styles/{style}', ['uses' => 'Settings\ImageStylesController@show', 'as' => 'api.settings.image_styles.show']);
Route::post('settings/image-styles', ['uses' => 'Settings\ImageStylesController@store', 'as' => 'api.settings.image_styles.store']);
Route::patch('settings/image-styles/{style}', ['uses' => 'Settings\ImageStylesController@update', 'as' => 'api.settings.image_styles.update']);

/* USERS */
Route::get('users', ['uses' => 'Users\UsersController@index', 'as' => 'api.users']);


