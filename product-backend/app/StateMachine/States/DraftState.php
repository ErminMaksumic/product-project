<?php

namespace App\StateMachine\States;

use App\Http\Requests\ActivateRequest;
use App\Http\Requests\VariantCreateRequest;
use App\Services\ProductService;
use App\Services\VariantService;
use App\StateMachine\Enums\ProductActions;
use App\StateMachine\Enums\ProductStatus;
use Illuminate\Support\Facades\Auth;

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

    public function addVariant(VariantCreateRequest $request)
    {
        $data = $request->all();
        $variant = $this->variantService->insert($data);
        return $variant;
    }

    public function activate($request, $product)
    {
        $data = $request->all();
        $data['status'] = ProductStatus::ACTIVATED->value;
        $data['activatedBy'] = Auth::user()->email;
        $product->update($data);
        return $product;
    }
}
