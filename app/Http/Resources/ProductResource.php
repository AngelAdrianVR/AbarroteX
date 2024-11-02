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
            'currency' => $this->currency,
            'cost' => $this->cost,
            'code' => $this->code,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'current_stock' => $this->current_stock,
            'description' => $this->description,
            'product_on_request' => $this->product_on_request,
            'show_in_online_store' => $this->show_in_online_store,
            'bulk_product' => $this->bulk_product,
            'measure_unit' => $this->measure_unit,
            'days_for_delivery' => $this->days_for_delivery,
            'store_id' => $this->store_id,
            'imageCover' => $this->getMedia('imageCover')->all(),
            'category' => $this->whenLoaded('category'),
            'brand' => $this->whenLoaded('brand'),
            'created_at' => $this->created_at?->isoFormat('DD MMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM YYYY'),
        ];
    }
}
