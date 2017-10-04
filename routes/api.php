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

Route::post('users/register', 'usersController@registration');
Route::get('tasks', 'tasksController@index');
Route::get('tasks/{task}', 'tasksController@watchTask');

Route::group(['prefix' => 'tasks', 'middleware' => 'auth:api'], function () {
    // CRUD Operation.
    Route::post('create', 'tasksController@create');
    Route::put('edit/{task}', 'tasksController@edit');
    Route::delete('delete/{task}', 'tasksController@delete');

    // Assign Deadline To Task.
    Route::post('deadline/{task}', 'tasksController@assignDeadline');
    // Toggle Task Status.
    Route::patch('toggleStatus/{task}', 'tasksController@toggleStatus');
    // Attach File To Task.
    Route::post('attachFile/{task}', 'tasksController@attachFile');

    // Invite Users To See Private Tasks.
    Route::post('invitation/send/{task}', 'tasksController@sendInvitation');
    Route::post('invitation/response/{task}', 'tasksController@invitationResponse');
});

