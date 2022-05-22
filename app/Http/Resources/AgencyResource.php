<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
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
            'role_id' => $this->role_id,
            'agency_name' => $this->agency_name,
            'registrationNumber' => $this->registrationNumber,
            'email' => $this->email,
            'bio' => $this->bio,
            'phone' => $this->phone,
            'picture' => asset('/images/users/' .  $this->profile_picture),
            'posts' => new Post($this->posts),

        ];
    }
}
