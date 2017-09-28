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

class deleteTaskTest extends TestCase
{
    /*use DatabaseTransactions, DatabaseMigrations;*/

    /** @test */
    function user_can_delete_his_task()
    {
        $task = factory(Task::class)->create();

        Passport::actingAs(User::find($task->user_id), ['api']);

        $response = $this->delete('api/task/delete/' . $task->id, [], ['Accept' => 'application/json']);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', $task->toArray());
    }

    /** @test */
    function user_cannot_delete_another_user_task()
    {
        $task = factory(Task::class)->create();

        Passport::actingAs(factory(User::class)->create(['password' => bcrypt('secret')]), ['api']);

        $response = $this->delete('api/task/delete/' . $task->id, [], ['Accept' => 'application/json']);

        $response->assertStatus(401);

        $this->assertDatabaseHas('tasks', $task->toArray());
    }

    /** @test */
    function guest_cannot_delete_any_user_task()
    {
        $task = factory(Task::class)->create();

        $response = $this->delete('api/task/delete/' . $task->id, [], ['Accept' => 'application/json']);

        $response->assertStatus(401);

        $this->assertDatabaseHas('tasks', $task->toArray());
    }
}