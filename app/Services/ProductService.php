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
            $query = $query->where(function ($query) use ($searchObject) {
                $query->orWhere('name', 'ILIKE', '%' . $searchObject->name . '%');
                $query->orWhereRaw("to_tsvector('english', name) @@ to_tsquery(?)", [$searchObject->name]);
            });
        }

        if ($searchObject->validFrom) {
            $query = $query->where('validFrom', '>=', $searchObject->validFrom);
        }

        if ($searchObject->validTo) {
            $query = $query->where('validTo', '<=', $searchObject->validTo);
        }

        if ($searchObject->priceGT || $searchObject->priceLT) {
            $query = $this->applyPriceFilter($query, $searchObject);
        }

        return $query;
    }

    private function applyPriceFilter($query, $searchObject)
    {
        return $query->with(['variants' => function ($variantQuery) use ($searchObject) {
            $this->addPriceConditions($variantQuery, $searchObject);
        }])
            ->whereHas('variants', function ($variantQuery) use ($searchObject) {
                $this->addPriceConditions($variantQuery, $searchObject);
            });
    }

    private function addPriceConditions($query, $searchObject)
    {
        if ($searchObject->priceGT) {
            $query->where('price', '>=', $searchObject->priceGT);
        }
        if ($searchObject->priceLT) {
            $query->where('price', '<', $searchObject->priceLT);
        }
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
        return new ProductSearchObject();
    }

    protected function getModelClass()
    {
        return new Product();
    }

    public function add($request)
    {
        $request['status'] = 'DRAFT';
        return parent::add($request);
    }
}
