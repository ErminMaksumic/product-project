<?php

namespace App\StateMachine\States;

use App\Services\ProductService;
use App\Services\VariantService;
use App\StateMachine\Enums\ProductStatus;

class DeleteState extends BaseState
{
    public function __construct(ProductService $service)
    {
        parent::__construct($service);
    }

    public function allowedActions()
    {
        $allowedActions = array();
        return $allowedActions;
    }
}
