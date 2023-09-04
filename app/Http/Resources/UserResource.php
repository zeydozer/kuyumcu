<?php

namespace App\Http\Resources;

use App\Http\Resources\CartResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'role' => $this->role,
            'name' => $this->name,
            'mail' => $this->mail,
            'token' => $this->token,
            'phone' => $this->phone,
            'address' => $this->address,
            'admin' => $this->admin,
            'carts' => CartResource::collection($this->whenLoaded('carts')),
            'created_at' => date('d.m.Y H:i', strtotime($this->created_at)),
            'updated_at' => date('d.m.Y H:i', strtotime($this->updated_at)),
            'deleted_at' => 
                $this->deleted_at ? 
                date('d.m.Y H:i', strtotime($this->deleted_at)) : 
                null,
        ];
    }
}
