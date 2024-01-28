<?php

namespace App\StateMachine;

use App\Exceptions\UserException;
use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;
use App\StateMachine\Config\StateConfiguration;
use App\StateMachine\Enums\ProductStatus;
use Exception;

class ProductStateMahineService
{

    public function __construct(protected StateConfiguration $stateConfiguration, protected ProductServiceInterface $productService)
    { }
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


    public function updateProduct(ProductStatus $productStatus, int $productId)
    {
        $collection = collect($this->allowedActions($productId));
        if ($collection->contains('value', $productStatus->value)) {
            $product = $this->productService->update(['status' => $productStatus], $productId);
        } else {
            throw new UserException("Status update not allowed!");
        }

        return $product;
    }

    public function productDraft(int $productId)
    {
       return  $this->updateProduct(ProductStatus::DRAFT, $productId);
    }

    public function productActivate(int $productId)
    {
        return  $this->updateProduct(ProductStatus::ACTIVATED, $productId);
    }
    public function productDelete(int $productId)
    {
        return  $this->updateProduct(ProductStatus::DELETED, $productId);
    }
}
