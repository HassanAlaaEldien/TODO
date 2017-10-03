<?php

namespace Tests\Feature\Task;

use App\Notifications\UserInvitations;
use App\Task;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\Passport;
use Tests\TestCase;

class InvitationsTest extends TestCase
{

    /** @test */
    public function authorized_user_can_send_invitation_to_other_users_to_see_his_private_tasks()
    {
        Notification::fake();

        list($task, $user, $response) = $this->send_invitation();

        $response->assertStatus(200);

        Notification::assertSentTo($user, UserInvitations::class);
    }

    /** @test */
    public function authorized_user_can_accept_or_reject_invitation()
    {

        list($task, $user, $response) = $this->send_invitation();

        Passport::actingAs(User::find($user->id), ['api']);

        $response = $this->post('api/task/invitation/response/' . $task->id, ['reply' => 'yes']);

        $response->assertStatus(201);
    }

    /** @test */
    public function only_invited_user_who_can_accept_or_reject_invitation()
    {

        list($task, $user, $response) = $this->send_invitation();

        $uninvited_user = factory(User::class)->create(['password' => bcrypt('secret')]);

        Passport::actingAs($uninvited_user, ['api']);

        $response = $this->post('api/task/invitation/response/' . $task->id, ['reply' => 'yes']);

        $response->assertStatus(403);
    }

    /**
     * @return array
     */
    protected function send_invitation()
    {
        $task = factory(Task::class)->create();

        $user = factory(User::class)->create(['password' => bcrypt('secret')]);

        Passport::actingAs(User::find($task->user_id), ['api']);

        $response = $this->post('api/task/invitation/send/' . $task->id, ['user' => $user->id]);

        return array($task, $user, $response);
    }
}
