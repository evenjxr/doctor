<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('login/index',['uses' => 'Login@index', 'as' => 'login.index']);

Route::post('login',['uses' => 'Login@login', 'as' => 'login']);

Route::get('loginout',['uses' => 'Login@loginOut', 'as' => 'loginout']);

Route::group(['middleware' => 'manager.login'], function () {

    Route::get('/', function () {
        return view('desktop');
    });

    Route::group(['prefix' => 'common'], function () {
        
        Route::post('upload',['uses' => 'Common@uploadFiles', 'as' => 'common.upload']);

    });

    Route::group(['prefix' => 'system'], function () {

        Route::get('add',['uses' => 'System@add', 'as' => 'system.add']);

        Route::post('store',['uses' => 'System@store', 'as' => 'system.store']);

        Route::get('lists',['uses' => 'System@lists', 'as' => 'system.lists']);

        Route::get('detail',['uses' => 'System@detail', 'as' => 'system.detail']);

        Route::post('update',['uses' => 'System@update', 'as' => 'system.update']);

        Route::post('delete',['uses' => 'System@delete', 'as' => 'system.delete']);

    });

    Route::group(['prefix' => 'user'], function () {

        Route::get('lists', ['uses' => 'User@lists', 'as' => 'user.lists']);

        Route::get('add', ['uses' => 'User@add', 'as' => 'user.add']);

        Route::post('store', ['uses' => 'User@store', 'as' => 'user.store']);

        Route::post('update', ['uses' => 'User@update', 'as' => 'user.update']);

        Route::get('detail', ['uses' => 'User@detail', 'as' => 'user.detail']);

        Route::post('delete', ['uses' => 'User@delete', 'as' => 'user.delete']);

        Route::get('auth', ['uses' => 'User@auth', 'as' => 'user.auth']);

    });

    Route::group(['prefix' => 'hospital'], function () {

        Route::get('lists', ['uses' => 'Hospital@lists', 'as' => 'hospital.lists']);

        Route::get('add', ['uses' => 'Hospital@add', 'as' => 'hospital.add']);

        Route::post('store', ['uses' => 'Hospital@store', 'as' => 'hospital.store']);

        Route::post('update', ['uses' => 'Hospital@update', 'as' => 'hospital.update']);

        Route::get('detail', ['uses' => 'Hospital@detail', 'as' => 'hospital.detail']);

        Route::post('delete', ['uses' => 'Hospital@delete', 'as' => 'hospital.delete']);

        Route::get('auth', ['uses' => 'Hospital@auth', 'as' => 'hospital.auth']);

    });


    Route::group(['prefix' => 'order'], function () {

        Route::get('lists', ['uses' => 'Order@lists', 'as' => 'order.lists']);

        Route::get('add', ['uses' => 'Order@add', 'as' => 'order.add']);

        Route::post('store', ['uses' => 'Order@store', 'as' => 'order.store']);

        Route::post('update', ['uses' => 'Order@update', 'as' => 'order.update']);

        Route::get('detail', ['uses' => 'Order@detail', 'as' => 'order.detail']);

        Route::get('transfer', ['uses' => 'Order@transfer', 'as' => 'order.transfer']);

        Route::post('delete', ['uses' => 'Order@delete', 'as' => 'order.delete']);

        Route::get('auth', ['uses' => 'Order@auth', 'as' => 'order.auth']);

        Route::get('pic', ['uses' => 'Order@pic', 'as' => 'order.pic']);

    });

    Route::group(['prefix' => 'manager'], function () {

        Route::get('index', ['uses' => 'Manager@index', 'as' => 'manager.index']);

        Route::get('lists', ['uses' => 'Manager@lists', 'as' => 'manager.lists']);

        Route::get('add', ['uses' => 'Manager@add', 'as' => 'manager.add']);

        Route::post('store', ['uses' => 'Manager@store', 'as' => 'manager.store']);

        Route::post('update', ['uses' => 'Manager@update', 'as' => 'manager.update']);

        Route::get('detail', ['uses' => 'Manager@detail', 'as' => 'manager.detail']);

        Route::get('delete', ['uses' => 'Manager@delete', 'as' => 'manager.delete']);

        Route::get('auth', ['uses' => 'Manager@auth', 'as' => 'manager.auth']);

    });

    Route::group(['prefix' => 'role'], function () {

        Route::get('lists', ['uses' => 'Role@lists', 'as' => 'role.lists']);

        Route::get('add', ['uses' => 'Role@add', 'as' => 'role.add']);

        Route::post('store', ['uses' => 'Role@store', 'as' => 'role.store']);

        Route::post('update', ['uses' => 'Role@update', 'as' => 'role.update']);

        Route::get('detail', ['uses' => 'Role@detail', 'as' => 'role.detail']);

        Route::post('delete', ['uses' => 'Role@delete', 'as' => 'role.delete']);
    });

    Route::group(['prefix' => 'tag'], function () {

        Route::get('lists', ['uses' => 'Tag@lists', 'as' => 'tag.lists']);

        Route::get('add', ['uses' => 'Tag@add', 'as' => 'tag.add']);

        Route::post('store', ['uses' => 'Tag@store', 'as' => 'tag.store']);

        Route::post('update', ['uses' => 'Tag@update', 'as' => 'tag.update']);

        Route::get('detail', ['uses' => 'Tag@detail', 'as' => 'tag.detail']);

        Route::post('delete', ['uses' => 'Tag@delete', 'as' => 'tag.delete']);

    });

    Route::group(['prefix' => 'score'], function () {

        Route::get('lists', ['uses' => 'Score@lists', 'as' => 'score.lists']);

        Route::get('add', ['uses' => 'Score@add', 'as' => 'score.add']);

        Route::post('store', ['uses' => 'Score@store', 'as' => 'score.store']);

        Route::post('update', ['uses' => 'Score@update', 'as' => 'score.update']);

        Route::get('detail', ['uses' => 'Score@detail', 'as' => 'score.detail']);

        Route::post('delete', ['uses' => 'Score@delete', 'as' => 'score.delete']);

    });

    Route::group(['prefix' => 'flower'], function () {

        Route::get('lists', ['uses' => 'Flower@lists', 'as' => 'flower.lists']);

        Route::get('add', ['uses' => 'Flower@add', 'as' => 'flower.add']);

        Route::post('store', ['uses' => 'Flower@store', 'as' => 'flower.store']);

        Route::post('update', ['uses' => 'Flower@update', 'as' => 'flower.update']);

        Route::get('detail', ['uses' => 'Flower@detail', 'as' => 'flower.detail']);

        Route::post('delete', ['uses' => 'Flower@delete', 'as' => 'flower.delete']);

    });

    Route::group(['prefix' => 'auth'], function () {

        Route::get('lists', ['uses' => 'Auth@lists', 'as' => 'auth.lists']);

        Route::get('add', ['uses' => 'Auth@add', 'as' => 'auth.add']);

        Route::post('store', ['uses' => 'Auth@store', 'as' => 'auth.store']);

        Route::post('update', ['uses' => 'Auth@update', 'as' => 'auth.update']);

        Route::get('detail', ['uses' => 'Auth@detail', 'as' => 'auth.detail']);

        Route::post('delete', ['uses' => 'Auth@delete', 'as' => 'auth.delete']);

    });

});
