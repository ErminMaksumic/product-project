<?php

namespace App\Http\Requests\SearchObjects;

class ProductSearchObject extends BaseSearchObject
{

    public ?string $name = null;
    public ?bool $includeProductType = null;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes)
    {
        parent::fill($attributes);
        $this->name = $attributes['name'] ?? null;
        $this->includeProductType = $attributes['includeProductType'] ?? null;
    }
}
