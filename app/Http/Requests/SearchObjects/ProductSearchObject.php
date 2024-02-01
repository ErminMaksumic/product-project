<?php

namespace App\Http\Requests\SearchObjects;

class ProductSearchObject extends BaseSearchObject
{

    public ?string $name = null;
    public ?bool $includeProductType = null;
    public ?bool $includeVariants = null;
    public ?int $priceGT = null;
    public ?int $priceLT = null;
    public ?string $validFrom = null;
    public ?string $validTo = null;

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
