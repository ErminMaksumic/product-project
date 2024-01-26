<?php

namespace App\Http\Requests\SearchObjects;

class ProductTypeSearchObject extends BaseSearchObject
{
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes)
    {
        parent::fill($attributes);
    }
}
