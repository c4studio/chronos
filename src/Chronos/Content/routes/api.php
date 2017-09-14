<?php

/* CONTENT */
Route::get('content/manage/{type}', ['uses' => 'ContentController@index', 'as' => 'api.content']);
Route::get('content/manage/{type}/export', ['uses' => 'ContentController@export', 'as' => 'api.content.export']);
Route::delete('content/manage/{type}/{content}', ['uses' => 'ContentController@destroy', 'as' => 'api.content.destroy']);
Route::delete('content/manage/{type}', ['uses' => 'ContentController@destroy_bulk', 'as' => 'api.content.destroy_bulk']);
Route::patch('content/manage/{type}/{content}/fieldset', ['uses' => 'ContentController@fieldset', 'as' => 'api.content.fieldset']);
Route::get('content/manage/{type}/{content}', ['uses' => 'ContentController@show', 'as' => 'api.content.show']);
Route::get('content/manage/{type}/{content}/translate', ['uses' => 'ContentController@translate', 'as' => 'api.content.translate']);
Route::post('content/manage/{type}', ['uses' => 'ContentController@store', 'as' => 'api.content.store']);
Route::patch('content/manage/{type}/{content}', ['uses' => 'ContentController@update', 'as' => 'api.content.update']);

/* CONTENT TYPES */
Route::get('content/types/export', ['uses' => 'ContentTypesController@export', 'as' => 'api.content.types.export']);
Route::resource('content/types', 'ContentTypesController', ['except' => ['create', 'edit'], 'names' => [
    'index' => 'api.content.types',
    'destroy' => 'api.content.types.destroy',
    'show' => 'api.content.types.show',
    'store' => 'api.content.types.store',
    'update' => 'api.content.types.update'
]]);
Route::delete('content/types', ['uses' => 'ContentTypesController@destroy_bulk', 'as' => 'api.content.types.destroy_bulk']);
Route::delete('content/types/field/{field}', ['uses' => 'ContentTypesController@destroy_field', 'as' => 'api.content.types.destroy_field']);
Route::delete('content/types/fieldset/{fieldset}', ['uses' => 'ContentTypesController@destroy_fieldset', 'as' => 'api.content.types.destroy_fieldset']);
Route::patch('content/types/{type}/fieldset', ['uses' => 'ContentTypesController@fieldset', 'as' => 'api.content.types.fieldset']);

/* MEDIA */
Route::get('content/media', ['uses' => 'MediaController@index', 'as' => 'api.content.media']);
Route::delete('content/media', ['uses' => 'MediaController@destroy_bulk', 'as' => 'api.content.media.destroy_bulk']);
Route::delete('content/media/{media}', ['uses' => 'MediaController@destroy', 'as' => 'api.content.media.destroy']);
Route::get('content/media/{media}', ['uses' => 'MediaController@show', 'as' => 'api.content.media.show']);
Route::post('content/media', ['uses' => 'MediaController@store', 'as' => 'api.content.media.store']);

/* SETTINGS */
Route::get('settings/languages', ['uses' => 'Settings\LanguagesController@index', 'as' => 'api.settings.languages']);
Route::get('settings/languages/all', ['uses' => 'Settings\LanguagesController@all', 'as' => 'api.settings.languages.all']);
Route::get('settings/languages/{language}/activate', ['uses' => 'Settings\LanguagesController@activate', 'as' => 'api.settings.languages.activate']);
Route::get('settings/languages/{language}/deactivate', ['uses' => 'Settings\LanguagesController@deactivate', 'as' => 'api.settings.languages.deactivate']);
Route::post('settings/languages', ['uses' => 'Settings\LanguagesController@store', 'as' => 'api.settings.languages.store']);