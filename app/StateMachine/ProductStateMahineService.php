<?php

namespace App\StateMachine;

use App\Exceptions\UserException;
use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;
use App\Services\Interfaces\VariantServiceInterface;
use App\StateMachine\Config\StateConfiguration;
use App\StateMachine\Enums\ProductStatus;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProductStateMahineService
{

    public function __construct(protected StateConfiguration $stateConfiguration, protected VariantServiceInterface $variantService)
    {
    }

    public function productDraft(int $productId)
    {
        return $this->updateProduct(ProductStatus::DRAFT, $productId);
    }

    public function updateProduct(ProductStatus $productStatus, int $productId, $validFrom = null, $validTo = null)
    {
        $allowedActions = $this->allowedActions($productId);

        if (!$this->isStatusUpdateAllowed($productStatus, $allowedActions)) {
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

    private function isStatusUpdateAllowed(ProductStatus $productStatus, $allowedActions)
    {
        return collect($allowedActions)->contains('value', $productStatus->value);
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
        return $this->updateProduct(ProductStatus::ACTIVATED, $productId, $validFrom, $validTo);
    }

    public function productDelete(int $productId)
    {
        return $this->updateProduct(ProductStatus::DELETED, $productId);
    }

    public function InsertToDraft($request)
    {
        $product = Product::find($request['product_id']);
        if ($product->status === ProductStatus::DRAFT->value)
        {
            return $this->variantService->add($request);
        } else
            throw new UserException("Not allowed!");
    }

    public function DraftToActive($request, int $productId)
    {
        $user = Auth::user();
        $product = Product::find($productId);
        $collection = collect($this->allowedActions($productId));

        if ($collection->contains('value', 'DraftToActive'))
        {
            $product->update([
                'status' => ProductStatus::ACTIVATED,
                'activatedBy' => $user->email,
                'valid_from' => $request['valid_from'],
                'valid_to' => $request['valid_to']
            ]);
        } else {
            throw new UserException('Not allowed!');
        }

        return $product;
    }

    public function ActiveToDeleted(int $productId)
    {
        $product = Product::find($productId);
        $collection = collect($this->allowedActions($productId));

        if ($collection->contains('value', 'ActiveToDelete'))
        {
            $product->update([
                'status' => ProductStatus::DELETED
            ]);
        } else {
            throw new UserException('Not allowed!');
        }

        return $product;
    }
}
