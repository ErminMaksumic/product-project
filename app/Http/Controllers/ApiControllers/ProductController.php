<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ActivateRequest;
use App\Http\Requests\ProductInsertRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\SearchObjects\ProductSearchObject;
use App\Http\Requests\VariantCreateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\VariantResource;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends BaseController
{
    public function __construct(protected ProductServiceInterface $productService)
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

    public function allowedActions(int $id)
    {
        return $this->productService->allowedActions($id);
    }

    public function productDraft(int $productId)
    {
        return ProductResource::make($this->productService->draftProduct($productId));
    }

    public function productHide(int $productId)
    {
        return ProductResource::make($this->productService->hideProduct($productId));
    }

    public function addVariant(Request $request)
    {
        $formRequestInstance = new VariantCreateRequest();
        $validatedData = $this->validate($request, $formRequestInstance->rules());

        return VariantResource::make($this->productService->addVariant($validatedData));
    }

    public function productActivate(ActivateRequest $request, int $productId)
    {
        $formRequestInstance = new ActivateRequest();
        $validatedData = $this->validate($request, $formRequestInstance->rules());

        return ProductResource::make($this->productService->activate($productId, $validatedData));
    }

    public function getSearchObject($params)
    {
        return new ProductSearchObject($params);
    }
}
