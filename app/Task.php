<?php

namespace App;

use Carbon\Carbon;
use Faker\Provider\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Task extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task', 'status'
    ];

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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function deadline()
    {
        return $this->hasOne('App\TaskDeadline');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany('App\taskAttachments');
    }

    /**
     * Add Task Operation.
     *
     * @param $data
     */
    public function add($data)
    {
        Auth::user()->tasks()->create([
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
            'task' => $data['task']
        ]);
    }

    /**
     * Assign Deadline To Task.
     *
     * @param $data
     */
    public function assign($data)
    {
        $this->deadline()->create([
            'deadline' => $data['deadline']
        ]);
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
}
