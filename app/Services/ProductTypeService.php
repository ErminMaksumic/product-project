<?php

namespace App\Services;

use App\Http\Requests\SearchObjects\ProductTypeSearchObject;
use App\Models\ProductType;
use App\Services\Interfaces\ProductTypeServiceInterface;
use Illuminate\Support\Facades\Cache;

class ProductTypeService extends BaseService implements ProductTypeServiceInterface
{
    public function addFilter($searchObject, $query){

        if($searchObject->name)
        {
            $query->where('name', 'ILIKE', '%' . $searchObject->name . '%');
        }

        return $query;
    }

    public function includeRelation($searchObject, $query){

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

    public function getPageable($searchObject)
    {
        $cacheKey = $this->generateCacheKey(request()->query());
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        $all = parent::getPageable($searchObject);
        Cache::put($cacheKey, $all, now()->addMinutes(1));

        return $all;
    }

    public function add(array $request)
    {
        $this->clearCache();
        $request['status'] = 'DRAFT';
        return parent::add($request);
    }

    public function update(array $request, int $id)
    {
        $this->clearCache();
        return parent::update($request, $id);
    }

    public function getById(int $id, $searchObject)
    {
        $this->clearCache();
        return parent::getById($id, $searchObject);
    }

    protected function generateCacheKey($parameters)
    {
        ksort($parameters);
        return 'product_types_' . http_build_query($parameters);
    }

    public function clearCache()
    {
        $keys = Cache::getStore()->getPrefix() . 'productType;*';
        Cache::forget($keys);
    }
}
