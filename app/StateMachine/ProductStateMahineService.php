<?php

namespace App\StateMachine;

use App\Exceptions\UserException;
use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;
use App\Services\VariantService;
use App\StateMachine\Config\StateConfiguration;
use App\StateMachine\Enums\ProductStatus;
use App\StateMachine\States\ActiveState;
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
                throw new Exception("Action not allowed!");
        }
    }

    public function insert($request)
    {
        $product = Product::find($request['product_id']);

        if(!$product)
        {
            throw new UserException("Product not found!");
        }
        $state = $this->createState($product->status);

        return $state->store($request);
    }

    public function update($request, int $id)
    {
        $variant = $this->variantService->getById($id);

        $state = $this->createState($variant->product->status);

        return $state->update($request, $id);
    }
}
