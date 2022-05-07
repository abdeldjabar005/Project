<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'comment', 'user_id', 'post_id',
    ];


    public function user(){

        return $this->belongsTo(User::class,'user_id','id');

    }

    public function post(){

        return $this->belongsTo(Post::class,'post_id','id');

    }
}
