<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
            'image_post_id' => $this->post_id,
//            'image_url' => $this->image_url,
            'imageUrl'  => asset('/images/posts/' .  $this->image_url)

        ];

    }

}
