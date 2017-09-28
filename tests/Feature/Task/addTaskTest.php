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

        $response = $this->post('api/task/create', $task->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', $task->toArray());
    }

    /** @test */
    function unauthorized_user_cannot_create_task()
    {
        $task = factory(Task::class)->make();

        $response = $this->post('api/task/create', $task->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('tasks', $task->toArray());
    }

    /** @test */
    function guest_cannot_create_task()
    {
        $task = factory(Task::class)->make();

        unset($task->user_id);

        $response = $this->post('api/task/create', $task->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('tasks', $task->toArray());
    }
}