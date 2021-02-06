<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::apiResource('users', 'UsersApiController');

    // Locations
    Route::post('locations/media', 'LocationsApiController@storeMedia')->name('locations.storeMedia');
    Route::apiResource('locations', 'LocationsApiController');

    // Project Types
    Route::post('project-types/media', 'ProjectTypesApiController@storeMedia')->name('project-types.storeMedia');
    Route::apiResource('project-types', 'ProjectTypesApiController');

    // Projects
    Route::post('projects/media', 'ProjectApiController@storeMedia')->name('projects.storeMedia');
    Route::apiResource('projects', 'ProjectApiController');
});
