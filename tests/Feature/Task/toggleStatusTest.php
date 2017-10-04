<?php

namespace Tests\Feature\Task;

use App\Task;
use App\TaskDeadline;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class toggleStatusTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:refresh');
    }

    /** @test */
    public function authorized_user_can_toggle_only_his_tasks_status()
    {
        $task = factory(Task::class)->create();

        Passport::actingAs(User::find($task->user_id), ['api']);

        $task_new_status = ['status' => 'public'];

        $response = $this->patch('api/tasks/toggleStatus/' . $task->id, $task_new_status, ['Accept' => 'application/json']);

        $response->assertStatus(200);

        $this->assertNotEquals($task->status, Task::find($task->id)->status);

        $this->assertEquals($task_new_status['status'], Task::find($task->id)->status);
    }

}