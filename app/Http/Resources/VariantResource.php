<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantResource extends JsonResource
{
    public $id;
    public $name;
    public $product;
    public $value;

    public function toArray(Request $request): array
    {
        // get only public properties
        $properties = get_object_vars($this);

        // assign values
        foreach ($this->resource->getAttributes() as $key => $value) {
            if (array_key_exists($key, $properties)) {
                $this->$key = $this->resource[$key];
            }
        }

        // relations
        $this->product = new ProductResource($this->whenLoaded('product'));


        // return attributes without default attributes
        $attributes = get_object_vars($this);
        unset($attributes['resource'], $attributes['additional'], $attributes['with']);
        return $attributes;
    }
}
