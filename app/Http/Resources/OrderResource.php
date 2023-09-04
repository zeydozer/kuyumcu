<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user'),
            'auth_id' => $this->auth_id,
            'auth' => $this->whenLoaded('auth'),
            'note' => $this->note,
            'quantity' => number_format($this->quantity, 0, ',', '.'),
            'weight' => number_format($this->weight, 2, ',', '.'),
            'status' => $this->status,
            'carts' => $this->whenLoaded('carts'),
            'finished_at' => date('d.m.Y', strtotime($this->finished_at)),
            'created_at' => date('d.m.Y H:i', strtotime($this->created_at)),
            'updated_at' => date('d.m.Y H:i', strtotime($this->updated_at)),
            'deleted_at' => 
                $this->deleted_at ? 
                date('d.m.Y H:i', strtotime($this->deleted_at)) : 
                null,
        ];
    }
}
