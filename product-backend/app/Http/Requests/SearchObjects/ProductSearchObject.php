<?php

namespace App\Http\Requests\SearchObjects;

use DateTime;

class ProductSearchObject extends BaseSearchObject
{

    public ?string $name = null;
    public ?bool $includeProductType = null;
    public ?bool $includeVariants = null;
    public ?int $priceGT = null;
    public ?int $priceLT = null;
    public ?DateTime $validFrom = null;
    public ?DateTime $validTo = null;

    public function __set($key, $value)
    {
        parent::__set($key, $value);
        $this->$key = $value;
    }

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

    }
}
