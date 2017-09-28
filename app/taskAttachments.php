<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class taskAttachments extends Model
{
    protected $fillable = [
        'attachment',
    ];

    public function task()
    {
        return $this->belongsTo('App\Task');
    }
}
