<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
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
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'messages' => new Message($this->messages),
        ];
    }
}
