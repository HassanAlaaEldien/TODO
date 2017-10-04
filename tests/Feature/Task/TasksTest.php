<?php
/**
 * Created by PhpStorm.
 * User: Hassan Alaa
 * Date: 04/10/2017
 * Time: 09:43 ص
 */

namespace Tests\Feature\Task;

use App\Notifications\NotifyWatchedTasks;
use App\Task;
use App\User;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\Passport;
use Tests\TestCase;

class tasksTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:refresh');
    }

    /** @test */
    public function anyone_can_watch_all_public_tasks()
    {
        $tasks = factory(Task::class, 9)->create(['status' => 'public']);

        $response = $this->get('api/tasks');

        $response->assertStatus(200);

        $response->assertJson(['success' => true, 'tasks' => $tasks->toArray()]);
    }

    /** @test */
    public function guest_can_watch_public_task()
    {
        $task = factory(Task::class)->create(['status' => 'public']);

        $response = $this->get('api/tasks/' . $task->id);

        $response->assertStatus(200);

        $response->assertJson(['success' => true, 'task' => $task->toArray()]);
    }

    /** @test */
    public function user_can_watch_public_task()
    {
        $task = factory(Task::class)->create(['status' => 'public']);

        $user = factory(User::class)->create(['password' => bcrypt('secret')]);

        Passport::actingAs($user);

        Notification::fake();

        $response = $this->get('api/tasks/' . $task->id);

        $response->assertStatus(200);

        Notification::assertSentTo(User::find($task->user_id), NotifyWatchedTasks::class);

        $response->assertJson(['success' => true, 'task' => $task->toArray()]);
    }
}