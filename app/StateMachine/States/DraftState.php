<?php

namespace App\StateMachine\States;

use App\Models\Product;
use App\Models\Variant;
use App\StateMachine\Enums\ProductActions;
use App\Services\VariantService;
use App\StateMachine\Enums\ProductStatus;

class DraftState extends BaseState
{
    public function __construct(VariantService $service)
    {
        parent::__construct($service);
    }
    public function store($request)
    {
        return $this->service->add($request);
    }

    public function update($request, int $id)
    {
        return $this->service->update($request, $id);
    }

    public function allowedActions()
    {
        $allowedActions = array();
        array_push($allowedActions, ProductActions::DraftToActive);
        return $allowedActions;
    }
}
