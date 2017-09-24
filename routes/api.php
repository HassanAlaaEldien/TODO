<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('user/register', 'userController@registration');

Route::group(['prefix' => 'task', 'middleware' => 'auth:api'], function () {
    Route::post('create', 'taskController@create');
    Route::put('edit/{task}', 'taskController@edit');
    Route::delete('delete/{task}', 'taskController@delete');
});

