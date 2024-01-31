<?php

namespace App\StateMachine\States;

use App\StateMachine\Enums\ProductStatus;

class DeleteState extends BaseState
{
    public function allowedActions()
    {
        $allowedActions = array();
        return $allowedActions;
    }
}
