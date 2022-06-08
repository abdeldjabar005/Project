<?php

namespace App\Http\Resources;

use App\Http\Resources\Like;
use App\Like as LikeModel;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->agency_id,
            'agency_name' => $this->agency_name,
            "picture" => asset('/images/users/' .  $this->user->profile_picture),
            'bio' => $this->user->bio,
            'phone' => $this->user->phone,
            'type'=>$this->type,
            'title'=>$this->title,
            'description' => $this->description,
            'location'=>$this->location,
            'price'=>$this->price,
            'space'=>$this->space,
            'bedrooms'=>$this->bedrooms,
            'bathrooms'=>$this->bathrooms,
            'garages'=>$this->garages,
            'longitude'=>$this->longitude,
            'latitude'=>$this->latitude,
            'comments' => new Comment($this->comments),
            'images' => new Image($this->images),
            'Likes' => new Like($this->likes),
            'Tags' => new Tag($this->tags),
            'createdAt' => $this->created_at->toDateTimeString(),
        ];
    }
}
