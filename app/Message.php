<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [

        'id','message','user_id','conversation_id'
    ];

    public function user(){

        return $this->belongsTo(User::class,'user_id','id');
    }
    public function conversation(){

        return $this->belongsTo(Conversation::class,'id','conversation_id');
    }
}
