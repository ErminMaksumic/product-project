<?php

namespace App\StateMachine\States;

use App\Exceptions\UserException;
use App\Models\Product;
use App\Services\ProductService;
use App\StateMachine\Enums\ProductActions;
use App\Services\VariantService;
use App\StateMachine\Enums\ProductStatus;
use Illuminate\Support\Facades\Auth;

class ActiveState extends BaseState
{
    public function __construct(protected ProductService $productService, protected VariantService $variantService)
    {
        parent::__construct($productService, $variantService);
    }

    public function allowedActions()
    {
        $allowedActions = array();
        array_push($allowedActions, ProductActions::DraftToActive);
        array_push($allowedActions, ProductActions::ActiveToDelete);
        return $allowedActions;
    }

    public function hideProduct($id)
    {
        return $this->updateProductModel(
            ProductActions::ActiveToDelete,
            ProductStatus::DELETED,
            $id);
    }
}
