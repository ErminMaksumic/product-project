<?php

namespace App\StateMachine;

use App\Exceptions\UserException;
use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;
use App\Services\VariantService;
use App\StateMachine\Config\StateConfiguration;
use App\StateMachine\Enums\ProductActions;
use App\StateMachine\Enums\ProductStatus;
use App\StateMachine\States\ActiveState;
use App\StateMachine\States\BaseState;
use App\StateMachine\States\DeleteState;
use App\StateMachine\States\DraftState;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductStateMahineService
{
    public function __construct(protected StateConfiguration $stateConfiguration, protected ProductServiceInterface $productService,
    protected VariantService $variantService)
    {
    }

    public function productDraft(int $productId)
    {
        return $this->updateProduct(ProductActions::DraftToActive, ProductStatus::DRAFT, $productId);
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



    public function insert($request)
    {
        $product = Product::find($request['product_id']);

        if(!$product)
        {
            throw new UserException("Product not found!");
        }
        $state = BaseState::createState($product->status);

        return $state->store($request);
    }

    public function update($request, int $id)
    {
        $variant = $this->variantService->getById($id);

        $state = BaseState::createState($variant->product->status);

        return $state->update($request, $id);
    }
}
