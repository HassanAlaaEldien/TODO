<?php

namespace App;

use App\Events\InviteUser;
use App\Notifications\UserInvitations;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
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
}
