<?php

namespace App\StateMachine\States;

use App\Services\ProductService;
use App\Services\VariantService;

class DeleteState extends BaseState
{
    public function __construct(protected ProductService $productService, protected VariantService $variantService)
    {
        parent::__construct($productService, $variantService);
    }

    public function allowedActions()
    {
        $allowedActions = array();
        return $allowedActions;
    }
}
