<?php

namespace Tests\Feature;

use App\Task;
use App\taskAttachments;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;

class attachFilesTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:refresh');
    }

    /** @test */
    public function user_can_attach_files_to_his_task()
    {
        $task = factory(Task::class)->create();

        Passport::actingAs(User::find($task->user_id), ['api']);

        $response = $this->post('/api/tasks/attachFile/' . $task->id, [
            'file' => UploadedFile::fake()->create('document.pdf', 40)
        ]);

        $response->assertStatus(200);

        Storage::disk('local')->assertExists($task->attachments->attachment);
    }

}
