<?php

namespace App;

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
        'task',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function deadline()
    {
        return $this->hasOne('App\TaskDeadline');
    }

    public function add($data)
    {
        Auth::user()->tasks()->create([
            'task' => $data['task']
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
}
