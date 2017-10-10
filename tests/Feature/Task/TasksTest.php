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

    /** @test */
    public function user_feed_contain_his_tasks_also_tasks_he_watched()
    {
        list($tasks, $other_user_task) = $this->create_and_watch_tasks();

        $response = $this->get('api/users/feed');

        $response->assertStatus(200);

        $response->assertJson(['success' => true, 'tasks' => array_merge($tasks->toArray(), [$other_user_task->toArray()])]);
    }

    /**
     * @return array
     */
    protected function create_and_watch_tasks()
    {
        $user = factory(User::class)->create(['password' => bcrypt('secret')]);

        $tasks = factory(Task::class, 5)->create(['user_id' => $user->id]);

        $other_user_task = factory(Task::class)->create(['status' => 'public']);

        Passport::actingAs($user);

        $this->get('api/tasks/' . $other_user_task->id);

        return array($tasks, $other_user_task);
    }
}