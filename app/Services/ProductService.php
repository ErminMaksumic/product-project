<?php

namespace App\Services;

use App\Http\Requests\SearchObjects\ProductSearchObject;
use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;

class ProductService extends BaseService implements ProductServiceInterface
{
    public function addFilter($searchObject, $query)
    {
        if ($searchObject->name) {
            $query = $query->where('name', $searchObject->name);
        }

        if ($searchObject->validFrom) {
            $query = $query->where('validFrom', '>=', $searchObject->validFrom);
        }

        if ($searchObject->validTo) {
            $query = $query->where('validTo', '<=', $searchObject->validTo);
        }

        if ($searchObject->priceGT > 0 || $searchObject->priceLT > 0)
            return $query->with(['variants' => function ($variantQuery) use ($searchObject) {
                if ($searchObject->priceGT) {
                    $variantQuery->where('price', '>', $searchObject->priceGT);
                }
                if ($searchObject->priceLT) {
                    $variantQuery->where('price', '<', $searchObject->priceLT);
                }
            }]);


        return $query;
    }


    public function includeRelation($searchObject, $query)
    {
        if ($searchObject->includeProductType) {
            $query = $query->with('productType');
        }

        return $query;
    }

    public function getSearchObject()
    {
        return ProductSearchObject::class;
    }

    public function add(array $request)
    {
        $request['status'] = 'DRAFT';
        return parent::add($request);
    }

    protected function getModelClass()
    {
        return Product::class;
    }
}
