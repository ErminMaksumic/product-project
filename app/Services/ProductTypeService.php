<?php

namespace App\Services;

use App\Http\Requests\ProductTypeCreateRequest;
use App\Http\Requests\ProductTypeUpdateRequest;
use App\Http\Requests\SearchObjects\ProductTypeSearchObject;
use App\Models\ProductType;
use App\Services\Interfaces\ProductTypeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductTypeService extends BaseService implements ProductTypeServiceInterface
{
    public function addFilter($searchObject, $query)
    {

        if ($searchObject->name) {
            $query->where('name', 'ILIKE', '%' . $searchObject->name . '%');
        }

        return $query;
    }

    public function includeRelation($searchObject, $query)
    {

        return $query;
    }

    public function getSearchObject($params)
    {
        return new ProductTypeSearchObject($params);
    }

    protected function getModelClass()
    {
        return new ProductType();
    }

    public function getInsertRequestClass()
    {
        return ProductTypeCreateRequest::class;
    }

    public function getUpdateRequestClass()
    {
        return ProductTypeUpdateRequest::class;
    }

    public function getPageable($searchObject)
    {
        $all = parent::getPageable($searchObject);

        return $all;
    }

    public function add(Request $request)
    {
        $request['status'] = 'DRAFT';

        $this->forgetCachedData('all_product_types');
        return parent::add($request);
    }

    public function update(Request $request, int $id)
    {
        $this->forgetCachedData('all_product_types');
        $this->forgetCachedData('one_product_type');
        return parent::update($request, $id);
    }

    public function getById(int $id, $searchObject)
    {
        return parent::getById($id, $searchObject);
    }

    protected function getCachedName($key = 'getPageable')
    {
        $cacheNames = [
            'getPageable' => 'all_product_types',
            'getOne' => 'one_product_type',
        ];
        return $cacheNames[$key] ?? $cacheNames['getPageable'];
    }
}
