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
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'product_type_id' => new ProductTypeResource($this->whenLoaded('productType')),
            'validFrom' => $this->validFrom,
            'validTo' => $this->validTo,
            'status' => $this->status,
            'variants' => VariantResource::collection($this->whenLoaded('variants'))
        ];

        if (!is_null($this->activatedBy)) {
            $data['activatedBy'] = $this->activatedBy;
        }

        if ($this->getNewestVariant()) {
            $data['newestVariant'] = $this->getNewestVariant();
        }

        return $data;

    }

    private function getNewestVariant()
    {
        if ($this->variants->isEmpty()) {
            return null;
        }

        $newestVariant = $this->variants->sortByDesc('created_at')->first();

        return [
            'id' => $newestVariant->id,
            'name' => $newestVariant->name,
            'price' => $newestVariant->price,
        ];
    }
}
