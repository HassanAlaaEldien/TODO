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
    // CRUD Operation.
    Route::post('create', 'taskController@create');
    Route::put('edit/{task}', 'taskController@edit');
    Route::delete('delete/{task}', 'taskController@delete');

    // Assign Deadline To Task.
    Route::post('deadline/{task}', 'taskController@assignDeadline');
    // Toggle Task Status.
    Route::patch('toggleStatus/{task}', 'taskController@toggleStatus');
    // Attach File To Task.
    Route::post('attachFile/{task}', 'taskController@attachFile');
});

