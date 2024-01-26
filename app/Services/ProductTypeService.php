<?php

namespace App\Services;

use App\Http\Requests\SearchObjects\BaseSearchObject;
use App\Models\ProductType;
use App\Services\Interfaces\ProductTypeServiceInterface;

class ProductTypeService extends BaseService implements ProductTypeServiceInterface
{
    public function addFilter($searchObject, $query){

        return $query;
    }

    public function includeRelation($searchObject, $query){

        return $query;
    }

    public function getSearchObject()
    {
        return BaseSearchObject::class;
    }

    protected function getModelClass()
    {
        return ProductType::class;
    }
}
