<?php

namespace App\Services;

use App\Http\Requests\SearchObjects\BaseSearchObject;
use App\Models\Variant;
use App\Services\Interfaces\VariantServiceInterface;

class VariantService extends BaseService implements VariantServiceInterface
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
        return Variant::class;
    }
}
