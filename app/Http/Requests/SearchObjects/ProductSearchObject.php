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

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes)
    {
        parent::fill($attributes);
        $this->name = $attributes['name'] ?? null;
        $this->priceGT = $attributes['priceGT'] ?? null;
        $this->priceLT = $attributes['priceLT'] ?? null;
        $this->validFrom = $attributes['validFrom'] ?? null;
        $this->validTo = $attributes['validTo'] ?? null;
        $this->includeProductType = $attributes['includeProductType'] ?? null;
        $this->includeVariants = $attributes['includeVariants'] ?? null;
    }
}
