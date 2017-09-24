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

        $response = $this->delete('api/task/delete/' . $task->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', $task->toArray());

    }
}