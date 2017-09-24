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

class editTaskTest extends TestCase
{
    /*use DatabaseTransactions, DatabaseMigrations;*/

    /** @test */
    function user_can_edit_his_task()
    {
        $task = factory(Task::class)->create();

        Passport::actingAs(User::find($task->user_id), ['api']);

        $new_task = factory(Task::class)->make(['user_id' => $task->user_id]);

        $response = $this->put('api/task/edit/' . $task->id, $new_task->toArray());

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', $task->toArray());

        $this->assertDatabaseHas('tasks', $new_task->toArray());

    }
}