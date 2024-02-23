<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Http\Requests\SearchObjects\BaseSearchObject;
use App\Http\Requests\SearchObjects\VariantSearchObject;
use App\Http\Requests\VariantCreateRequest;
use App\Http\Requests\VariantUpdateRequest;
use App\Models\Product;
use App\Models\Variant;
use App\Services\Interfaces\VariantServiceInterface;
use App\StateMachine\States\BaseState;
use Illuminate\Http\Request;

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

    public function getSearchObject($params)
    {
        return new VariantSearchObject($params);
    }

    protected function getModelClass()
    {
        return new Variant();
    }

    public function getInsertRequestClass()
    {
        return VariantCreateRequest::class;
    }

    public function getUpdateRequestClass()
    {
        return VariantUpdateRequest::class;
    }

    public function add($request)
    {
        $product = Product::find($request['product_id']);
        $state = BaseState::createState($product->status);

        return $state->addProduct($request);
    }

    public function insert($request)
    {
        $model = Variant::create($request);
        return $model;
    }

    public function update(Request $request, int $id)
    {
        $model = Variant::find($id);

        if(!$model)
        {
            throw new UserException("Resource not found!");
        }

        $model->update($request->all());

        return $model;
    }
}
