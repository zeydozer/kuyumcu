<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product'),
            'height' => $this->whenLoaded('height'),
            'width' => $this->width,
            'weight' => number_format($this->weight, 2, ',', '.'),
            'photo' => $this->photo,
            'note' => $this->note,
            'quantity' => number_format($this->quantity, 0, ',', '.'),
            'weight_total' => number_format($this->weight_total, 2, ',', '.'),
            'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at)),
            'updated_at' => date('Y-m-d H:i:s', strtotime($this->updated_at)),
            'deleted_at' => 
                $this->deleted_at ? 
                date('Y-m-d H:i:s', strtotime($this->deleted_at)) : 
                null,
        ];
    }
}
