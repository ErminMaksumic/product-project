<?php

namespace App\Http\Resources;

use App\Models\ProductType;
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
            'description' => $this->description,
            'product_type_id' => new ProductTypeResource($this->whenLoaded('productType')),
            'validFrom' => $this->validFrom,
            'validTo' => $this->validTo,
            'status' => $this->status,
            'variants' => VariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
