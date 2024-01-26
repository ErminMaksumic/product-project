<?php

namespace App\Services;

use App\Http\Requests\SearchObjects\BaseSearchObject;
use App\Http\Requests\SearchObjects\VariantSearchObject;
use App\Models\Variant;
use App\Services\Interfaces\VariantServiceInterface;

class VariantService extends BaseService implements VariantServiceInterface
{
    public function addFilter($searchObject, $query){

        return $query;
    }

    public function includeRelation($searchObject, $query){

        if($searchObject->includeProduct)
        {
            $query = $query->with('product');
        }

        return $query;
    }

    public function getSearchObject()
    {
        return VariantSearchObject::class;
    }

    protected function getModelClass()
    {
        return Variant::class;
    }
}
