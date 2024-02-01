<?php

namespace App\Http\Requests\SearchObjects;

class BaseSearchObject
{
    public int $page = 1;
    public int $size = 10;

    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }
}
