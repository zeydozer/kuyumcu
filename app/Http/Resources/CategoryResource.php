<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $resource = [
            'id' => $this->id,
            'name' => $this->name,
            'root' => new CategoryResource($this->whenLoaded('root')),
            'childs' => CategoryResource::collection($this->whenLoaded('childs')),
            'created_at' => date('d.m.Y H:i', strtotime($this->created_at)),
            'updated_at' => date('d.m.Y H:i', strtotime($this->updated_at)),
            'deleted_at' =>
                $this->deleted_at ?
                date('d.m.Y H:i', strtotime($this->deleted_at)) :
                null
        ];
        if ($this->products_count) {
            $childsIds = $this->childsId();
            $resource['product_count'] = Product::where('ctg_id', $this->id)
                ->orWhereIn('ctg_id', $childsIds)
                ->selectRaw('COUNT(id) AS quantity')
                ->value('quantity');
        }
        return $resource;
    }
}