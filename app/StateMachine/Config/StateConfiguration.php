<?php

namespace App\StateMachine\Config;

use App\StateMachine\Enums\ProductStatus;
use App\StateMachine\States\DraftState;
use App\StateMachine\States\ActiveState;
use App\StateMachine\States\DeleteState;

class StateConfiguration
{
    private $draftState;
    private $deletedState;
    private $activatedState;

    public function __construct(
        DraftState  $draftState,
        ActiveState $activatedState,
        DeleteState $deletedState,
    ) {
        $this->draftState = $draftState;
        $this->activatedState = $activatedState;
        $this->deletedState = $deletedState;
    }

    public function stateMap()
    {
        return [
            ProductStatus::DRAFT->name => $this->draftState,
            ProductStatus::ACTIVATED->name => $this->activatedState,
            ProductStatus::DELETED->name => $this->deletedState,
        ];
    }
}
