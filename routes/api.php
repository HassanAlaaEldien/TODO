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

Route::post('users/register', 'usersController@registration')->name('userRegistration');
Route::get('tasks', 'tasksController@index')->name('allTasks');
Route::get('tasks/{task}', 'tasksController@watchTask')->name('specificTask');

Route::group(['middleware' => 'auth:api'], function () {

    Route::group(['prefix' => 'tasks'], function () {
        // CRUD Operation.
        Route::post('create', 'tasksController@create')->name('createTask');
        Route::put('edit/{task}', 'tasksController@edit')->name('updateTask');
        Route::delete('delete/{task}', 'tasksController@delete')->name('deleteTask');

        // Assign Deadline To Task.
        Route::post('deadline/{task}', 'tasksController@assignDeadline')->name('assignTaskDeadline');
        // Toggle Task Status.
        Route::patch('toggleStatus/{task}', 'tasksController@toggleStatus')->name('toggleTaskStatus');
        // Attach File To Task.
        Route::post('attachFile/{task}', 'tasksController@attachFile')->name('attachTaskFile');

        // Invite Users To See Private Tasks.
        Route::post('invitation/send/{task}', 'tasksController@sendInvitation')->name('sendInvitation');
        Route::post('invitation/response/{task}', 'tasksController@invitationResponse')->name('respondToInvitation');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('feed', 'usersController@userFeed')->name('userFeed');
        Route::get('search', 'usersController@search')->name('search');
        Route::post('changePassword', 'usersController@changePassword')->name('changePassword');
        Route::put('updatePersonalInfo', 'usersController@updatePersonalInfo')->name('updatePersonalInfo');
    });
});

