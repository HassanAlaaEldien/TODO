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

    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:refresh');
    }

    /** @test */
    function user_can_edit_his_task()
    {
        $task = factory(Task::class)->create();

        Passport::actingAs(User::find($task->user_id), ['api']);

        $new_task = factory(Task::class)->make(['task' => 'New Task !!!', 'user_id' => $task->user_id]);

        $response = $this->put('api/tasks/edit/' . $task->id, $new_task->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(200);


        $this->assertDatabaseMissing('tasks', $task->toArray());

        $this->assertDatabaseHas('tasks', $new_task->toArray());

    }

    /** @test */
    function user_cannot_edit_another_user_task()
    {

        $task = factory(Task::class)->create();

        $new_task = factory(Task::class)->make(['task' => 'New Task !!!']);

        Passport::actingAs(User::find($new_task->user_id), ['api']);

        $response = $this->put('api/tasks/edit/' . $task->id, $new_task->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(401);

        $this->assertDatabaseHas('tasks', $task->toArray());

        $this->assertDatabaseMissing('tasks', $new_task->toArray());
    }

    /** @test */
    function guest_cannot_edit_any_user_task()
    {

        $task = factory(Task::class)->create();

        $new_task = factory(Task::class)->make(['task' => 'New Task !!!', 'user_id' => null]);

        $response = $this->put('api/tasks/edit/' . $task->id, $new_task->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(401);

        $this->assertDatabaseHas('tasks', $task->toArray());

        $this->assertDatabaseMissing('tasks', $new_task->toArray());
    }
}