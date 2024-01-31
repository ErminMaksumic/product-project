<?php

namespace App\StateMachine\States;

use App\Models\Product;
use App\StateMachine\Enums\ProductActions;
use App\Services\VariantService;
use App\StateMachine\Enums\ProductStatus;

class ActiveState extends BaseState
{
    public function __construct(VariantService $service)
    {
        parent::__construct($service);
    }

    public function allowedActions()
    {
        $allowedActions = array();
        array_push($allowedActions, ProductActions::DraftToActive);
        array_push($allowedActions, ProductActions::ActiveToDelete);
        return $allowedActions;
    }
}
