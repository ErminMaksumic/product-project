<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Http\Requests\SearchObjects\BaseSearchObject;
use App\Http\Requests\SearchObjects\VariantSearchObject;
use App\Models\Product;
use App\Models\Variant;
use App\Services\Interfaces\VariantServiceInterface;
use App\StateMachine\States\BaseState;

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

    public function add(array $request)
    {
        $product = Product::find($request['product_id']);

        if(!$product)
        {
            throw new UserException("Product not found!");
        }
        $state = BaseState::CreateState($product->status);

        return $state->store($request);
    }

    public function update(array $request, int $id)
    {
        $variant = $this->getById($id);

        $state = BaseState::CreateState($variant->product->status);

        return $state->update($request, $id);
    }
}
