<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class User extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     *
     */
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            UserResource::collection($this->collection),
        ];
    }
}
