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

    public function addProduct($request)
    {
        throw new Exception("Not allowed");
    }

    public function updateProduct(int $id, $request)
    {
        throw new Exception("Not allowed");
    }

    public function hideProduct(int $id)
    {
        throw new Exception("Not allowed");
    }

    public function productActivate(array $request, int $productId)
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

    public function updateProductModel(
        ProductActions $productAction,
        ProductStatus $productStatus,
        int $productId,
        $validFrom = null,
        $validTo = null)
    {
        $product = Product::find($productId);

        $updateData = ['status' => $productStatus];

        if ($validFrom && $validTo) {
            $updateData += [
                'validFrom' => $validFrom,
                'validTo' => $validTo,
                'activatedBy' => Auth::user()->email
            ];
        }

        $product->update($updateData);

        return $product;
    }

    private function isStatusUpdateAllowed(ProductActions $productAction, $allowedActions)
    {
        return collect($allowedActions)->contains('value', $productAction->value);
    }
}
