<?php

namespace App;

use App\Notifications\UserInvitations;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function watchedTasks()
    {
        return $this->belongsToMany('App\Task', 'watched_tasks', 'user_id', 'task_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function avatar()
    {
        return $this->hasOne('App\UserImage');
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

        $message = "$this->name invite you to see his private task ( $task->title ) .";

        $user->notify(new UserInvitations($task, $message));
    }

    /**
     * Respond To Invitation.
     *
     * @param $reply
     * @param Task $task
     */
    public function respondToInvitation($reply, $authorized, Task $task)
    {
        if ($reply == 'yes' && $authorized)
            Auth::user()->invitations()->attach($task->id);
    }


    /**
     * Get Authorized User Feed (Contains User Tasks and other tasks that he watched).
     * Also Can Filter Feed Tasks By (Date, Status, Owner).
     *
     * @param array $query
     * @return array
     */
    public function feed(array $query)
    {
        $tasksIDs = $tasks = array();

        $userFeed = $this->filterByOwner($query);

        foreach ($userFeed as $task)
            $tasksIDs[] = $task['id'];

        $tasks = Task::whereIn('id', $tasksIDs);

        $this->filterByStatus($query, $tasks);

        $this->filterByDate($query, $tasks);

        return $tasks->get()->toArray();
    }

    /**
     * Changing Authorized User Password.
     *
     * @param $new_password
     */
    public function changePassword($new_password)
    {
        Auth::user()->update([
            'password' => bcrypt($new_password)
        ]);
    }

    /**
     * Update User Personal Information & his Avatar.
     *
     * @param array $data
     */
    public function updateInfo(array $data, $image_path)
    {
        Auth::user()->update([
            'name' => $data['name'],
            'email' => $data['email']
        ]);

        if (!empty($image_path))
            $this->saveUserAvatar($image_path);
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


    /**
     * @param array $query
     * @param Task $tasks
     */
    protected function filterByStatus(array $query, $tasks)
    {
        if (array_key_exists("status", $query)) {
            if ($query['status'] === 'public')
                $tasks->where('status', 'public');

            if ($query['status'] === 'private')
                $tasks->where('status', 'private');
        }
    }


    /**
     * @param array $query
     * @param Task $tasks
     */
    protected function filterByDate(array $query, $tasks)
    {
        if (array_key_exists("date", $query)) {
            if ($query['date'] === 'asc')
                $tasks->orderBy('id', 'asc');

            if ($query['date'] === 'desc')
                $tasks->orderBy('id', 'DESC');
        }
    }


    /**
     * @param array $query
     * @return array
     */
    protected function filterByOwner(array $query)
    {
        $userFeed = array_merge(Auth::user()->tasks->toArray(), Auth::user()->watchedTasks->toArray());

        if (array_key_exists("owner", $query)) {
            if ($query['owner'] === 'mine')
                $userFeed = Auth::user()->tasks->toArray();

            if ($query['owner'] === 'watched')
                $userFeed = Auth::user()->watchedTasks->toArray();
        }

        return $userFeed;
    }

    /**
     * @param $old_password
     * @return mixed
     */
    public function checkUserOldPassword($old_password)
    {
        return Hash::check($old_password, Auth::user()->password);
    }

    /**
     * @param $old_password
     * @return mixed
     */
    public function checkEmailAvailbily($email)
    {
        return User::where('email', $email)->where('email', '!=', Auth::user()->email)->count();
    }

    /**
     * @param $image_path
     */
    protected function saveUserAvatar($image_path)
    {
        if (count((Auth::user()->avatar))) {
            Storage::delete(Auth::user()->avatar->avatar);
            Auth::user()->avatar()->update([
                'avatar' => $image_path
            ]);
        } else {
            Auth::user()->avatar()->create([
                'avatar' => $image_path
            ]);
        }
    }
}
