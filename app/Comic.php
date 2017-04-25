<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comic extends Model
{
    public function author()
    {
        return $this->belongsTo('App\Author');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function likes()
    {
        return $this->hasMany('App\UserLike');
    }

    public function commments()
    {
        return $this->hasMany('App\Comment');
    }
}
