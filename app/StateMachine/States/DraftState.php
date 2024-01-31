<?php

namespace App\StateMachine\States;

use App\Models\Product;
use App\Models\Variant;
use App\Services\ProductService;
use App\StateMachine\Enums\ProductActions;
use App\StateMachine\Enums\ProductStatus;

class DraftState extends BaseState
{
    public function __construct(ProductService $service)
    {
        parent::__construct($service);
    }

    public function allowedActions()
    {
        $allowedActions = array();
        array_push($allowedActions, ProductActions::DraftToActive);
        return $allowedActions;
    }

    public function addProduct($request)
    {
        $request['status'] = ProductStatus::DRAFT;
        $product = Product::create($request);
        return $product;
    }

    public function updateProduct($id, $request)
    {
        $product = Product::find($id);
        $product->update($request);
        return $product;
    }

    public function addVariant($request)
    {
        $variant = Variant::create($request);
        return $variant;
    }

    public function productActivate(array $request, int $productId)
    {
        return $this->updateProductModel(
            ProductActions::DraftToActive,
            ProductStatus::ACTIVATED,
            $productId,
            $request['valid_from'],
            $request['valid_to']);
    }


}
