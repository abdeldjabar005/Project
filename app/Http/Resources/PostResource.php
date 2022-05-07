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
            'description' => $this->description,
            'comments' => new Comment($this->comments),
            'images' => new Image($this->images),
            'Likes' => new Like($this->likes),
            'Tags' => new Tag($this->tags),
            'createdAt' => $this->created_at->toDateTimeString(),

        ];

    }
}