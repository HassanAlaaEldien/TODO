<?php

namespace App;

use Carbon\Carbon;
use Faker\Provider\DateTime;
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
        'task', 'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function deadline()
    {
        return $this->hasOne('App\TaskDeadline');
    }

    public function attachments()
    {
        return $this->hasMany('App\taskAttachments');
    }

    public function add($data)
    {
        Auth::user()->tasks()->create([
            'task' => $data['task'],
            'status' => isset($data['status']) ? $data['status'] ? $data['status'] : 'public' : 'public'
        ]);
    }

    public function edit($data)
    {
        $this->update([
            'task' => $data['task']
        ]);
    }

    public function assign($data)
    {
        $this->deadline()->create([
            'deadline' => $data['deadline']
        ]);
    }

    public function checkUserAccessibility()
    {
        return $this->user_id === Auth::user()->id ? true : false;
    }

    public function checkDeadlineAvailability($deadline)
    {
        $deadline = Carbon::parse(is_a($deadline, 'DateTime') ? $deadline->format('Y-m-d H:i:s') : $deadline);

        return $this->created_at->gt($deadline);
    }
}
