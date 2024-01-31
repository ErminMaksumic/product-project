<?php

namespace App\StateMachine\States;

use App\StateMachine\Enums\ProductStatus;
use ArrayObject;

class DeleteState extends BaseState
{
    public function allowedActions()
    {
        $allowedActions = array();
        return $allowedActions;
    }
}
