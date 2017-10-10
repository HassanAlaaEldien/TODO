<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class taskAttachments extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'attachment',
    ];

    /**
     * @var string
     */
    protected $table = 'task_attachments';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo('App\Task');
    }
}
