<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'id','sender_id','receiver_id',
    ];
    public function users(){

        return $this->belongsToMany(User::class,'users', 'id');
    }
    public function messages(){

        return $this->hasMany(Message::class,'conversation_id','id');
    }

}
