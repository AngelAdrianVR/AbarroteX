<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'public_price' => $this->public_price,
            'cost' => $this->cost,
            'code' => $this->code,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'current_stock' => $this->current_stock,
            'imageCover' => $this->getMedia('imageCover')->all(),
            'created_at' => $this->created_at?->isoFormat('DD MMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM YYYY'),
        ];
    }
}
