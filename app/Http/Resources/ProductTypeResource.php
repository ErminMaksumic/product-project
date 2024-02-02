<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductTypeResource extends JsonResource
{
    // Define public properties that match the expected attributes
    public $id;
    public $name;
    public $description;

    public function toArray(Request $request): array
    {
        $properties = get_object_vars($this);

        foreach ($this->resource->getAttributes() as $key => $value) {
            if (array_key_exists($key, $properties)) {
                $this->$key = $this->resource[$key];
            }
        }

        $attributes = get_object_vars($this);
        unset($attributes['resource'], $attributes['additional'], $attributes['with']);

        return $attributes;
    }
}
