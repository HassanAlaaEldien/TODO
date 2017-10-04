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
    /** @test */
    public function user_can_attach_files_to_his_task()
    {
        $task = factory(Task::class)->create();

        Passport::actingAs(User::find($task->user_id), ['api']);

        Storage::fake('Tasks');

        $response = $this->post('/api/tasks/attachFile/' . $task->id, [
            'file' => UploadedFile::fake()->create('document.pdf', 40)
        ], ['Accept' => 'application/json']);

        $response->assertStatus(200);

        Storage::disk('Tasks')->assertExists('files');
    }

}
