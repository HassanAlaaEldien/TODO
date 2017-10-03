<?php

namespace App;

use App\Notifications\UserInvitations;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany('App\Task');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function invitations()
    {
        return $this->belongsToMany('App\Task', 'invitation_user', 'user_id', 'task_id')->withTimestamps();
    }

    /**
     * User Registration.
     *
     * @param $user
     */
    public function register($user)
    {
        $this->create([
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => bcrypt($user['password']),
        ]);
    }

    /**
     * Send Invitation To See Private Tasks.
     *
     * @param $user
     * @param $task
     */
    public function sendInvitation($user, $task)
    {
        $user = User::find($user);

        $message = $this->name . ' invite you to see his private task (' . explode(' ', trim($task->task))[0] . ') .';

        $user->notify(new UserInvitations($task, $message));
    }

    /**
     * Respond To Invitation.
     *
     * @param $reply
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondToInvitation($reply, $authorized, Task $task)
    {
        if ($reply == 'yes' && $authorized)
            Auth::user()->invitations()->attach($task->id);
    }


    /**
     * Check if user Invited.
     *
     * @param Task $task
     * @return bool
     */
    public function checkIfUserInvited(Task $task)
    {
        $authorized = false;

        foreach (Auth::user()->notifications as $notification) {
            if ($notification->notifiable_id == Auth::user()->id && $notification->data['task']['id'] == $task->id)
                $authorized = true;
        }

        return $authorized;
    }
}
