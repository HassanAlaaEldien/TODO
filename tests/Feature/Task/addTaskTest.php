<?php
/**
 * Created by PhpStorm.
 * User: Hassan Alaa
 * Date: 19/09/2017
 * Time: 01:41 م
 */

namespace Tests\Feature\Task;


use App\Task;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;

class addTaskTest extends TestCase
{
    /*use DatabaseTransactions, DatabaseMigrations;*/

    /** @test */
    function user_can_create_task()
    {
        $task = factory(Task::class)->make();

        Passport::actingAs(User::find($task->user_id), ['api']);

        $response = $this->post('api/task/create', $task->toArray());

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', $task->toArray());
    }
}