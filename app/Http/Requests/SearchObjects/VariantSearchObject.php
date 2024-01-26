<?php

namespace App\Http\Requests\SearchObjects;

class VariantSearchObject extends BaseSearchObject
{
    public ?bool $includeProduct = null;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes)
    {
        parent::fill($attributes);
        $this->includeProduct = $attributes['includeProduct'] ?? null;
    }
}
