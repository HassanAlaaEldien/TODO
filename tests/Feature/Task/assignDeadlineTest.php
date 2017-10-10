<?php

namespace Tests\Feature\Task;

use App\TaskDeadline;
use App\User;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\Passport;
use Tests\TestCase;

class assignDeadlineTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:refresh');
    }

    /** @test */
    public function user_can_assign_deadline_to_his_task()
    {
        $deadline = factory(TaskDeadline::class)->make(['deadline' => '2017-10-12 11:12:14']);

        Passport::actingAs(User::find($deadline->task->user_id), ['api']);

        $response = $this->post('api/tasks/deadline/' . $deadline->task->id, $deadline->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(201);

        unset($deadline->task);

        $this->assertDatabaseHas('task_deadlines', $deadline->toArray());
    }

    /** @test */
    public function deadline_date_must_be_after_task_creation_date()
    {
        $deadline = factory(TaskDeadline::class)->make(['deadline' => '1984-11-02 11:12:14']);

        Passport::actingAs(User::find($deadline->task->user_id), ['api']);

        $response = $this->post('api/tasks/deadline/' . $deadline->task->id, $deadline->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(422);

        unset($deadline->task);

        $this->assertDatabaseMissing('task_deadlines', $deadline->toArray());

    }

    /** @test */
    public function user_cannot_assign_deadline_to_another_user_task()
    {
        $deadline = factory(TaskDeadline::class)->make();

        Passport::actingAs(factory(User::class)->create(['password' => bcrypt('secret')]), ['api']);

        $response = $this->post('api/tasks/deadline/' . $deadline->task->id, $deadline->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(401);

        unset($deadline->task);

        $this->assertDatabaseMissing('task_deadlines', $deadline->toArray());
    }

    /** @test */
    public function unauthorized_user_cannot_assign_deadline_to_his_task()
    {
        $deadline = factory(TaskDeadline::class)->make();

        $response = $this->post('api/tasks/deadline/' . $deadline->task->id, $deadline->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(401);

        unset($deadline->task);

        $this->assertDatabaseMissing('task_deadlines', $deadline->toArray());
    }
}
