<?php

namespace App\StateMachine\States;

use App\StateMachine\Enums\ProductStatus;

class DeleteState extends BaseState
{
    public function allowedActions()
    {
        return [
            ProductStatus::DRAFT,

        ];
    }
}
