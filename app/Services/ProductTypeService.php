<?php

namespace App\Services;

use App\Http\Requests\SearchObjects\ProductTypeSearchObject;
use App\Models\ProductType;
use App\Services\Interfaces\ProductTypeServiceInterface;
use Illuminate\Support\Facades\Cache;

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
        return new ProductTypeSearchObject();
    }

    protected function getModelClass()
    {
        return new ProductType();
    }

    public function getPageable()
    {
        if (Cache::has('product_types')) {
            return Cache::get('product_types');
        }

        $all = parent::getPageable();
        Cache::put('product_types', $all, 30);
        return $all;
    }

    public function add(array $request)
    {
        Cache::forget('product_type');
        $request['status'] = 'DRAFT';
        return parent::add($request);
    }

    public function update(array $request, int $id)
    {
        Cache::forget('product_type');
        return parent::update($request, $id);
    }

    public function getById(int $id)
    {
        Cache::forget('product_type');
        return parent::getById($id);
    }

}
