<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'role_id',
        'first_name',
        'last_name',
        'agency_name',
        'reg_number',
        "bio",
        'email',
        "profile_picture",
        'phone_number',
        'password',
        ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function posts(){

        return $this->hasMany(Post::class,'agency_id','id')->orderBy('updated_at', 'desc');

    }
    public function sender()
    {
        return $this->hasMany(Conversation::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->hasMany(Conversation::class, 'receiver_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class ,'user_id' );
    }


}
