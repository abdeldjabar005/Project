<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
            return [
                $this->mergeWhen($this->user->role_id === 1, [
                    'agency_id' => $this->user_id,
                    "user_name" => $this->user->agency_name,

                ]),
                $this->mergeWhen($this->user->role_id === 2, [
                   'agency_id' => $this->user_id,
                   "user_name" => $this->user->first_name,
                ]),
                "picture" => asset('/images/users/' .  $this->user->profile_picture),
                'post_id' => $this->post_id,
                'comment' => $this->comment,
                'commented_On' => $this->created_at->toFormattedDateString()
            ];
    }

}
