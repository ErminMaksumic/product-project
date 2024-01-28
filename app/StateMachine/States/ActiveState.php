<?php

namespace App\StateMachine\States;

use App\Models\Product;
use App\StateMachine\Enums\ProductStatus;

class ActiveState extends BaseState
{
    public function __construct()
    {
    }


    public function allowedActions()
    {
        return [
                ProductStatus::DRAFT,
        ];
    }
}
