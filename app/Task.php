<?php

namespace App;

use App\Jobs\DeadlineReminder;
use App\Notifications\NotifyWatchedTasks;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'task', 'status', 'slug'
    ];

    /**
     * @var string
     */
    protected $table = 'tasks';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function invitations()
    {
        return $this->belongsToMany('App\User', 'invitation_user', 'task_id', 'user_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function usersWatchedThisTask()
    {
        return $this->belongsToMany('App\User', 'watched_tasks', 'task_id', 'user_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function deadline()
    {
        return $this->hasOne('App\TaskDeadline');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function attachments()
    {
        return $this->hasOne('App\taskAttachments');
    }

    /**
     * Add Task Operation.
     *
     * @param $data
     */
    public function add($data)
    {
        Auth::user()->tasks()->create([
            'title' => $data['title'],
            'task' => $data['task'],
            'status' => isset($data['status']) ? $data['status'] ? $data['status'] : 'public' : 'public'
        ]);
    }

    /**
     * Edit Task Operation.
     *
     * @param $data
     */
    public function edit($data)
    {
        $this->update([
            'title' => $data['title'],
            'task' => $data['task']
        ]);
    }

    /**
     * Assign Deadline To Task.
     *
     * @param $data
     */
    public function assignDeadline($data)
    {
        $this->deadline()->create([
            'deadline' => $data['deadline']
        ]);

        $this->notifyAfterEightyPercent($data);
    }

    /**
     * Attach File To Task.
     *
     * @param $file
     */
    public function attachFile($file)
    {
        $this->attachments()->create([
            'attachment' => $file
        ]);
    }


    /**
     * Notify task owner that any user watch his task.
     *
     * @param $task
     */
    public function notifyTaskOwner($task)
    {
        $notifiable_user = User::find($task->user_id);

        $message = Auth::user()->name . " viewed your task ( $task->title ) .";

        $notifiable_user->notify(new NotifyWatchedTasks($message));

        Auth::user()->watchedTasks()->attach($task->id);
    }

    /**
     * Check If User Authorized To Make Any Operation On Specific Task.
     *
     * @return bool
     */
    public function checkUserAccessibility()
    {
        return $this->user_id === Auth::user()->id ? true : false;
    }

    /**
     * Check If Deadline of Task Is Available.
     *
     * @param $deadline
     * @return mixed
     */
    public function checkDeadlineAvailability($deadline)
    {
        $deadline = Carbon::parse(is_a($deadline, 'DateTime') ? $deadline->format('Y-m-d H:i:s') : $deadline);

        return $this->created_at->gt($deadline);
    }

    /**
     * @param $data
     */
    protected function notifyAfterEightyPercent($data)
    {
        $deadline = Carbon::parse(is_a($data['deadline'], 'DateTime') ? $data['deadline']->format('Y-m-d H:i:s') : $data['deadline']);

        $deadline_length = $deadline->diffInHours($this->created_at);

        $eighty_percent = Carbon::now()->diffInHours($this->created_at->addHours($deadline_length * .8));

        $remaining_length = $deadline_length - ($deadline_length * .8);

        DeadlineReminder::dispatch($this, $remaining_length)->delay(Carbon::now()->addHours($eighty_percent));
    }
}
