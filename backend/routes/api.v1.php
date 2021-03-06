<?php

use Api\V1\Http\Middleware\AppendClientIpAddress;
use Api\V1\Http\Middleware\DeletePhotoDirectory;
use Api\V1\Http\Middleware\FetchExifData;
use Api\V1\Http\Middleware\GenerateAvgColor;
use Api\V1\Http\Middleware\GenerateThumbnails;
use Api\V1\Http\Middleware\SaveUploadedPhotoFile;
use Api\V1\Presenters\PhotoPresenter;
use Api\V1\Presenters\PublishedPhotoPresenter;
use Api\V1\Presenters\SubscriptionPresenter;
use Api\V1\Presenters\TagPresenter;
use Api\V1\Presenters\TokenPresenter;
use Api\V1\Presenters\UserPresenter;
use Core\Models\Photo;
use Core\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the Api\V1\Providers\RouteServiceProvider within a group which
| is assigned the "api.v1" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return [
        'name' => config('app.name'),
        'version' => '1.0.0',
    ];
});

/*
|--------------------------------------------------------------------------
| Token Resource Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'token'], function () {

    Route::post('/')
        ->uses('TokenController@create')
        // Allow 20 requests per minute.
        ->middleware('throttle:20,1')
        ->middleware(sprintf('present:%s', TokenPresenter::class));

});

/*
|--------------------------------------------------------------------------
| Users Resource Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'users'], function () {

    Route::post('/')
        ->uses('UsersController@create')
        ->middleware(sprintf('can:create-resource,%s', User::class))
        ->middleware(sprintf('present:%s', UserPresenter::class));

    Route::get('/{user}')
        ->uses('UsersController@get')
        ->middleware('can:get-resource,user')
        ->middleware(sprintf('present:%s', UserPresenter::class));

    Route::put('/{user}')
        ->uses('UsersController@update')
        ->middleware('can:update-resource,user')
        ->middleware(sprintf('present:%s', UserPresenter::class));

    Route::delete('/{user}')
        ->uses('UsersController@delete')
        ->middleware('can:delete-resource,user')
        ->middleware(sprintf('present:%s', UserPresenter::class));

});

/*
|--------------------------------------------------------------------------
| Photos Resource Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'photos'], function () {

    Route::post('/')
        ->uses('PhotosController@create')
        ->middleware(sprintf('can:create-resource,%s', Photo::class))
        ->middleware(FetchExifData::class)
        ->middleware(SaveUploadedPhotoFile::class)
        ->middleware(GenerateThumbnails::class)
        ->middleware(GenerateAvgColor::class)
        ->middleware(sprintf('present:%s', PhotoPresenter::class));

    Route::get('/{photo}')
        ->uses('PhotosController@get')
        ->middleware('can:get-resource,photo')
        ->middleware(sprintf('present:%s', PhotoPresenter::class));

    Route::post('/{photo}')
        ->uses('PhotosController@update')
        ->middleware('can:update-resource,photo')
        ->middleware(FetchExifData::class)
        ->middleware(SaveUploadedPhotoFile::class)
        ->middleware(GenerateThumbnails::class)
        ->middleware(GenerateAvgColor::class)
        ->middleware(sprintf('present:%s', PhotoPresenter::class));

    Route::delete('/{photo}')
        ->uses('PhotosController@delete')
        ->middleware('can:delete-resource,photo')
        ->middleware(DeletePhotoDirectory::class)
        ->middleware(sprintf('present:%s', PhotoPresenter::class));

});

/*
|--------------------------------------------------------------------------
| Published Photos Resource Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'published_photos'], function () {

    Route::post('/')
        ->uses('PublishedPhotosController@create')
        ->middleware(sprintf('can:create-resource,%s', Photo::class))
        ->middleware(sprintf('present:%s', PublishedPhotoPresenter::class));

    Route::get('/')
        ->uses('PublishedPhotosController@find')
        ->middleware(sprintf('present:%s', PublishedPhotoPresenter::class));

    Route::get('/{published_photo}')
        ->uses('PublishedPhotosController@get')
        ->middleware(sprintf('present:%s', PublishedPhotoPresenter::class));

    Route::put('/{published_photo}')
        ->uses('PublishedPhotosController@update')
        ->middleware('can:update-resource,published_photo')
        ->middleware(sprintf('present:%s', PublishedPhotoPresenter::class));

    Route::delete('/{published_photo}')
        ->uses('PublishedPhotosController@delete')
        ->middleware('can:delete-resource,published_photo')
        ->middleware(DeletePhotoDirectory::class)
        ->middleware(sprintf('present:%s', PublishedPhotoPresenter::class));

});

/*
|--------------------------------------------------------------------------
| Tags Resource Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'tags'], function () {

    Route::get('/')
        ->uses('TagsController@find')
        ->middleware(sprintf('present:%s', TagPresenter::class));

});

/*
|--------------------------------------------------------------------------
| Contact Message Resource Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'contact_messages'], function () {

    Route::post('/')
        ->uses('ContactMessagesController@create')
        ->middleware(AppendClientIpAddress::class)
        // Allow 5 requests per minute.
        ->middleware('throttle:5,1');

});

/*
|--------------------------------------------------------------------------
| Subscription Resource Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'subscriptions'], function () {

    Route::post('/')
        ->uses('SubscriptionsController@create')
        ->middleware(sprintf('present:%s', SubscriptionPresenter::class));

    Route::delete('/{subscription}')
        ->uses('SubscriptionsController@delete')
        ->middleware(sprintf('present:%s', SubscriptionPresenter::class));

});
