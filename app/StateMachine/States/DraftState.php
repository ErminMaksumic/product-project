<?php

namespace App\StateMachine\States;

use App\Models\Product;
use App\Models\Variant;
use App\Services\ProductService;
use App\Services\VariantService;
use App\StateMachine\Enums\ProductActions;
use App\StateMachine\Enums\ProductStatus;

class DraftState extends BaseState
{
    public function __construct(protected ProductService $productService, protected VariantService $variantService)
    {
        parent::__construct($productService, $variantService);
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
        $product = $this->productService->insert($request);
        return $product;
    }

    public function updateProduct($id, $request)
    {
        $product = $this->productService->updateProduct($request, $id);
        return $product;
    }

    public function addVariant($request)
    {
        $variant = $this->variantService->insert($request);
        return $variant;
    }

    public function productActivate(array $request, int $productId)
    {
        $allowedActions = $this->allowedActions();

        return $this->updateProductModel(
            ProductActions::DraftToActive,
            ProductStatus::ACTIVATED,
            $productId,
            $allowedActions,
            $request['valid_from'],
            $request['valid_to']);
    }


}
