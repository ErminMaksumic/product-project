<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\VariantCreateRequest;
use App\Http\Requests\VariantUpdateRequest;
use App\Http\Resources\VariantResource;
use App\Services\Interfaces\VariantServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VariantController extends BaseController
{

    public function __construct(VariantServiceInterface $variantService)
    {
        parent::__construct($variantService);
    }

    public function getInsertRequestClass()
    {
        return VariantCreateRequest::class;
    }

    public function getUpdateRequestClass()
    {
        return VariantUpdateRequest::class;
    }

    public function createResourcePayload($request, $collection = false) : VariantResource | AnonymousResourceCollection
    {
        if($collection)
        {
            return VariantResource::collection($request);
        }

        return new VariantResource($request);
    }
}
