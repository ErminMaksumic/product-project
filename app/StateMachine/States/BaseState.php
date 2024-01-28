<?php

namespace App\StateMachine\States;

use Exception;

class BaseState
{
    public function store($request)
    {
        throw new Exception("Not allowed");
    }

    public function update($request, int $id)
    {
        throw new Exception("Not allowed");
    }

    static function CreateState($stateName)
    {
        switch ($stateName) {
            case ('ACTIVATED'):
                return new ActiveState();
            case ('DRAFT'):
                return new DraftState();
            case ('DELETED'):
                return new DeleteState();
            default:
                throw new Exception("Action not allowed!");
        }
    }
}
