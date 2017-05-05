<?php

Route::group(['middleware' => 'auth'], function () {

    /* CONTENT */
    Route::get('content/manage/{type}', ['uses' => 'ContentController@index', 'as' => 'chronos.content']);
    Route::get('content/manage/{type}/create', ['uses' => 'ContentController@create', 'as' => 'chronos.content.create']);
    Route::get('content/manage/{type}/{content}/edit', ['uses' => 'ContentController@edit', 'as' => 'chronos.content.edit']);
    Route::get('content/manage/{type}/{content}/fieldsets', ['uses' => 'ContentController@fieldsets', 'as' => 'chronos.content.fieldset']);

    /* CONTENT TYPES */
    Route::get('content/types', ['uses' => 'ContentTypesController@index', 'as' => 'chronos.content.types', 'middleware' => 'can:view_content_types']);
    Route::get('content/types/{type}/edit', ['uses' => 'ContentTypesController@edit', 'as' => 'chronos.content.types.edit', 'middleware' => 'can:edit_content_types']);
    Route::get('content/types/{type}/fieldsets', ['uses' => 'ContentTypesController@fieldsets', 'as' => 'chronos.content.types.fieldset', 'middleware' => 'can:edit_content_type_fieldsets']);

    /* MEDIA */
    Route::get('content/media', ['uses' => 'MediaController@index', 'as' => 'chronos.content.media']);
});