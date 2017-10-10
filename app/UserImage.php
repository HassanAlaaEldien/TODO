<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserImage extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'avatar',
    ];

    /**
     * @var string
     */
    protected $table = 'user_images';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
