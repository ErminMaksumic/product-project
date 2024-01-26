<?php

namespace App\Services;

use App\Http\Requests\SearchObjects\ProductSearchObject;
use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;

class ProductService extends BaseService implements ProductServiceInterface
{
    public function addFilter($searchObject, $query){

        if ($searchObject->name) {
            $query = $query->where('name', $searchObject->name);
        }

        return $query;
    }

    public function includeRelation($searchObject, $query){

        return $query;
    }

    public function getSearchObject()
    {
        return ProductSearchObject::class;
    }

    protected function getModelClass()
    {
        return Product::class;
    }
}
