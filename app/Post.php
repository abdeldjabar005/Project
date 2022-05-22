<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [

        'type','title','description', 'agency_id', "agency_name",'id','location','price', 'space','bedrooms','bathrooms','garages'
    ];

    public function user(){

        return $this->belongsTo(User::class,'agency_id','id');
    }
    public function images(){

        return $this->hasMany(Image::class,'post_id','id');
    }

    public function comments(){

        return $this->hasMany(Comment::class,'post_id','id');
    }


    public function likes(){

        return $this->hasMany(Like::class);
    }

    public function tags(){

        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }

}
