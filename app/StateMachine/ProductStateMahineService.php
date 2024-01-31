<?php

namespace App\StateMachine;

use App\Exceptions\UserException;
use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;
use App\StateMachine\Config\StateConfiguration;
use App\StateMachine\Enums\ProductActions;
use App\StateMachine\Enums\ProductStatus;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProductStateMahineService
{

    public function __construct(protected StateConfiguration $stateConfiguration, protected ProductServiceInterface $productService)
    {
    }

    public function productDraft(int $productId)
    {
        return $this->updateProduct(ProductActions::DraftToActive, ProductStatus::DRAFT, $productId);
    }

    public function updateProduct(ProductActions $productAction, ProductStatus $productStatus, int $productId, $validFrom = null, $validTo = null)
    {
        $allowedActions = $this->allowedActions($productId);

        if (!$this->isStatusUpdateAllowed($productAction, $allowedActions)) {
            throw new UserException("Status update not allowed!");
        }

        $updateData = ['status' => $productStatus];

        if ($validFrom && $validTo) {
            $updateData += [
                'validFrom' => $validFrom,
                'validTo' => $validTo,
                'activatedBy' => Auth::user()->email
            ];
        }

        return $this->productService->update($updateData, $productId);
    }

    private function isStatusUpdateAllowed(ProductActions $productAction, $allowedActions)
    {
        return collect($allowedActions)->contains('value', $productAction->value);
    }

    public function allowedActions(int $id)
    {
        $product = Product::query()->find($id);
        if (!$product) {
            throw new Exception("Product not found");
        }

        $status = ProductStatus::from($product->status);

        if ($status === null) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $state = $this->stateConfiguration->stateMap()[$status->name];
        return $state->allowedActions();
    }

    public function productActivate(int $productId, string $validFrom, string $validTo)
    {
        return $this->updateProduct(
            ProductActions::DraftToActive,
            ProductStatus::ACTIVATED,
            $productId,
            $validFrom,
            $validTo);
    }

    public function productDelete(int $productId)
    {
        return $this->updateProduct(ProductActions::ActiveToDelete, ProductStatus::DELETED, $productId);
    }
}
