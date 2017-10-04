<?php
/**
 * Created by PhpStorm.
 * User: Hassan Alaa
 * Date: 04/10/2017
 * Time: 09:43 ص
 */

namespace Tests\Feature\Task;

use App\Task;
use Tests\TestCase;

class tasksTest extends TestCase
{
    /** @test */
    public function get_all_public_tasks_test()
    {
        $tasks = factory(Task::class, 9)->create(['status' => 'public']);

        $response = $this->get('api/tasks');

        $response->assertStatus(200);

        $response->assertJson(['success' => true, 'tasks' => $tasks->toArray()]);
    }
}