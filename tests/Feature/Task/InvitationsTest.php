<?php

namespace Tests\Feature\Task;

use App\Notifications\UserInvitations;
use App\Task;
use App\User;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\Passport;
use Tests\TestCase;

class InvitationsTest extends TestCase
{

    /** @test */
    public function authorized_user_can_send_invitation_to_other_users_to_see_his_private_tasks()
    {
        $task = factory(Task::class)->create();

        $user = factory(User::class)->create(['password' => bcrypt('secret')]);

        Passport::actingAs(User::find($task->user_id), ['api']);

        Notification::fake();

        $response = $this->post('api/task/invitation/send/' . $task->id, ['user' => $user->id]);

        $response->assertStatus(200);

        Notification::assertSentTo(
            $user, UserInvitations::class
        );

    }

    /** @test */
    /*public function authorized_user_can_accept_or_reject_invitation()
    {

    }*/
}
