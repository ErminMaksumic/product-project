<?php

namespace App\Http\Requests\SearchObjects;

use DateTime;
use Illuminate\Foundation\Http\FormRequest;

class BaseSearchObject extends FormRequest
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
            if ($key === 'validFrom' || $key === 'validTo') {
                $this->$key = !empty($value) ? new DateTime($value) : null;
            } else {
                $this->$key = $value;
            }
        }
    }
}
