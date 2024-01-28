<?php

namespace App\StateMachine\States;

use App\Models\Product;
use App\Models\Variant;
use App\StateMachine\Enums\ProductStatus;

class DraftState extends BaseState
{
    public function store($request)
    {
        return Variant::create($request);
    }

    public function update($request, int $id)
    {
        $model = Variant::find($id);

        if (!$model) {
            abort(404, 'Resource not found');
        }

        $model->update($request);

        return $model;
    }

    public function allowedActions()
    {
        return [
            ProductStatus::ACTIVATED,
            ProductStatus::DELETED,
        ];
    }
}
