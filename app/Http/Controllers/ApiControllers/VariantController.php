<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\BaseController;
use App\Http\Requests\SearchObjects\VariantSearchObject;
use App\Http\Requests\VariantCreateRequest;
use App\Http\Requests\VariantUpdateRequest;
use App\Http\Resources\VariantResource;
use App\Services\Interfaces\VariantServiceInterface;
use App\StateMachine\ProductStateMahineService;
use Illuminate\Http\Request;
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

    public function createResourcePayload($request, bool $collection = false) : VariantResource | AnonymousResourceCollection
    {
        if($collection)
        {
            return VariantResource::collection($request);
        }

        return new VariantResource($request);
    }

    public function getSearchObject($params)
    {
        return new VariantSearchObject($params);
    }

}
