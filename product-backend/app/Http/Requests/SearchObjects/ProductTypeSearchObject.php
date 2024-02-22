<?php

namespace App\Http\Requests\SearchObjects;

class ProductTypeSearchObject extends BaseSearchObject
{
    public ?string $name = '';
    public function __set($key, $value)
    {
        parent::__set($key, $value);
        $this->$key = $value;
    }

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }

    }
}
