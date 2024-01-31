<?php

namespace App\StateMachine\States;

use App\Models\Product;
use App\StateMachine\Enums\ProductActions;
use App\StateMachine\Enums\ProductStatus;
use ArrayObject;

class ActiveState extends BaseState
{
    public function __construct()
    {  }


    public function allowedActions()
    {
        $allowedActions = array();
        array_push($allowedActions, ProductActions::DraftToActive);
        array_push($allowedActions, ProductActions::ActiveToDelete);
        return $allowedActions;
    }
}
