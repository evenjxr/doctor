<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:api');

Route::group(['prefix' => 'login'], function () {

    Route::post('sms',['uses' => 'Api\Login@sms']);

    Route::post('index',['uses' => 'Api\Login@index']);

});

Route::group(['prefix' => 'tag'], function () {

    Route::get('lists',['uses' => 'Api\Tag@lists']);

    Route::post('add',['uses' => 'Api\Tag@add']);

});

Route::group(['prefix' => 'user'], function () {

    Route::get('index',['uses' => 'Api\User@index']);

    Route::get('detail',['uses' => 'Api\User@detail']);

    Route::post('update',['uses' => 'Api\User@update']);

});

Route::group(['prefix' => 'wallet'], function () {

    Route::get('amount',['uses' => 'Api\Wallet@amount']);

    Route::get('lists',['uses' => 'Api\Wallet@lists']);

});

Route::group(['prefix' => 'score'], function () {

    Route::get('amount',['uses' => 'Api\Score@amount']);

    Route::get('lists',['uses' => 'Api\Score@lists']);

});

Route::group(['prefix' => 'order'], function () {

    Route::post('add',['uses' => 'Api\Order@add']);

    Route::get('accept/lists',['uses' => 'Api\Order@acceptLists']);

    Route::get('send/lists',['uses' => 'Api\Order@sendLists']);

});

Route::group(['prefix' => 'like'], function () {

    Route::post('add',['uses' => 'Api\Like@add']);

    Route::post('cancel',['uses' => 'Api\Like@cancel']);

    Route::get('lists',['uses' => 'Api\Like@lists']);

});

Route::group(['prefix' => 'flower'], function () {

    Route::post('add',['uses' => 'Api\Flower@add']);

    Route::post('cancel',['uses' => 'Api\Flower@cancel']);

    Route::get('lists',['uses' => 'Api\Flower@lists']);

});

Route::group(['prefix' => 'follow'], function () {

    Route::get('index',['uses' => 'Api\Follow@index']);

    Route::post('add',['uses' => 'Api\Follow@add']);

    Route::post('cancel',['uses' => 'Api\Follow@cancel']);

    Route::get('lists',['uses' => 'Api\Follow@lists']);

});

Route::group(['prefix' => 'garden'], function () {

    Route::post('update',['uses' => 'Api\Garden@update']);

    Route::get('detail',['uses' => 'Api\Garden@detail']);

});




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');