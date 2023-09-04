<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'photo' => $this->photo,
            'ctg_id' => $this->ctg_id,
            'width' => $this->width,
            'weight' => $this->weight,
            'between' => $this->between,
            'empty' => $this->empty,
            'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at)),
            'updated_at' => date('Y-m-d H:i:s', strtotime($this->updated_at)),
            'deleted_at' => 
                $this->deleted_at ? 
                date('Y-m-d H:i:s', strtotime($this->deleted_at)) : 
                null,
        ];
    }
}
