<?php

namespace App\StateMachine\States;

use App\Exceptions\UserException;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\VariantService;
use App\StateMachine\Enums\ProductActions;
use App\StateMachine\Enums\ProductStatus;
use Exception;
use Illuminate\Support\Facades\Auth;

class BaseState
{
    public function __construct(protected ProductService $productService, protected VariantService $variantService)
    { }

    public function allowedActions()
    {
        $allowedActions = array();
        return $allowedActions;
    }

    public function addProduct($request)
    {
        throw new Exception("Not allowed");
    }

    public function updateProduct(int $id, $request)
    {
        throw new Exception("Not allowed");
    }

    public function hideProduct($product)
    {
        throw new Exception("Not allowed");
    }

    public function activate($request, $model)
    {
        throw new Exception("Not allowed");
    }

    public function productDraft(int $productId)
    {
        throw new Exception("Not allowed");
    }

    public function addVariant($request)
    {
        throw new Exception("Not allowed");
    }

    static function createState($stateName)
    {
        switch ($stateName) {
            case 'ACTIVATED':
                return app('ActiveState');
            case 'DRAFT':
                return app('DraftState');
            case 'DELETED':
                return app('DeleteState');
            default:
                throw new Exception("Creating state: Action not allowed!");
        }
    }
}
