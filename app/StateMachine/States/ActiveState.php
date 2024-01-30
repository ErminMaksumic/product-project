<?php

namespace App\StateMachine\States;

use App\Models\Product;
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
        return [
            ProductStatus::DRAFT,
            ProductStatus::DELETED,
        ];
    }
}
