<?php

namespace App\StateMachine\States;

use App\Services\VariantService;
use Exception;

class BaseState
{
    public function __construct(protected VariantService $service)
    { }

    public function store($request)
    {
        throw new Exception("Not allowed");
    }

    public function update($request, int $id)
    {
        throw new Exception("Not allowed");
    }
}
