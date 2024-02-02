<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public $id;
    public $name;
    public $description;
    public $validFrom;
    public $validTo;
    public $status;
    public $productType;
    public $variants;
    public $activatedBy;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        // get only public properties
        $properties = get_object_vars($this);

        // assign values
        foreach ($this->resource->getAttributes() as $key => $value) {
            if (array_key_exists($key, $properties)) {
                $this->$key = $value;
            }
        }

        // relations
        $this->productType = new ProductTypeResource($this->whenLoaded('productType'));
        $this->variants = VariantResource::collection($this->whenLoaded('variants'));

        $newestVariant = $this->getNewestVariant();
        if ($newestVariant) {
            $this->newestVariant = $newestVariant;
        }

        // return attributes without default attributes
        $attributes = get_object_vars($this);
        unset($attributes['resource'], $attributes['additional'], $attributes['with']);

        return $attributes;
    }

    private function getNewestVariant()
    {

        // if there is no variants, return null
        if ($this->variants instanceof AnonymousResourceCollection) {
            return null;
        }

        $newestVariant = $this->variants->sortByDesc('created_at')->first();

        return [
            'id' => $newestVariant['id'],
            'name' => $newestVariant['name'],
            'price' => $newestVariant['price'],
        ];
    }
}
