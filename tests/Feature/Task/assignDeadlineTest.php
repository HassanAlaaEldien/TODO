<?php

namespace Tests\Feature\Task;

use App\Task;
use App\TaskDeadline;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class assignDeadlineTest extends TestCase
{
    /** @test */
    public function user_can_assign_deadline_to_his_task()
    {
        $deadline = factory(TaskDeadline::class)->make();

        Passport::actingAs(User::find($deadline->task->user_id), ['api']);

        $response = $this->post('api/task/deadline/' . $deadline->task->id, $deadline->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(201);

        unset($deadline->task);

        $this->assertDatabaseHas('task_deadlines', $deadline->toArray());
    }

    /** @test */
    public function user_cannot_assign_deadline_to_another_user_task()
    {
        $deadline = factory(TaskDeadline::class)->make();

        Passport::actingAs(factory(User::class)->create(['password' => bcrypt('secret')]), ['api']);

        $response = $this->post('api/task/deadline/' . $deadline->task->id, $deadline->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(401);

        unset($deadline->task);

        $this->assertDatabaseMissing('task_deadlines', $deadline->toArray());
    }

    /** @test */
    public function unauthorized_user_cannot_assign_deadline_to_his_task()
    {
        $deadline = factory(TaskDeadline::class)->make();

        $response = $this->post('api/task/deadline/' . $deadline->task->id, $deadline->toArray(), ['Accept' => 'application/json']);

        //$response->assertStatus(401);

        unset($deadline->task);

        $this->assertDatabaseMissing('task_deadlines', $deadline->toArray());
    }
}
