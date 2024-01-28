<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ProductInsertRequest;
use App\Http\Requests\ProductTypeCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;
use App\StateMachine\ProductStateMahineService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends BaseController
{
    public function __construct(ProductServiceInterface $productService, protected ProductStateMahineService $productStateMahineService)
    {
        parent::__construct($productService);
    }

    public function getInsertRequestClass()
    {
        return ProductInsertRequest::class;
    }

    public function getUpdateRequestClass()
    {
        return ProductUpdateRequest::class;
    }

    public function createResourcePayload($request, $collection = false) : ProductResource | AnonymousResourceCollection
    {
        if($collection)
        {
            return ProductResource::collection($request);
        }

        return new ProductResource($request);
    }

    public function newestVariant()
    {
        $productsWithNewestVariant = Product::with(['variants' => function ($query) {
            $query->latest()->take(1);
        }])->get();

        dd($productsWithNewestVariant);

        $structuredData = $productsWithNewestVariant->map(function ($product) {
            $newestVariant = $product->variants->first();

            return [
                'product_id' => $product->id,
                'name' => $product->name,
                'newest_variant_id' => $newestVariant ? $newestVariant->id : null,
                'newest_variant_name' => $newestVariant ? $newestVariant->name : null,
                'newest_variant_price' => $newestVariant ? $newestVariant->price : null,
            ];
        });

        return response()->json(['data' => $structuredData]);
    }




    public function allowedActions(int $id)
    {
        return $this->productStateMahineService->allowedActions($id);
    }

    public function productDraft(int $orderId)
    {
        return ProductResource::make($this->productStateMahineService->productDraft($orderId));
    }

    public function productActivate(int $orderId)
    {
        return ProductResource::make($this->productStateMahineService->productActivate($orderId));
    }

    public function productDelete(int $orderId)
    {
        return ProductResource::make($this->productStateMahineService->productDelete($orderId));
    }
}
