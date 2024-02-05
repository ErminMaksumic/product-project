<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Http\Requests\ActivateRequest;
use App\Http\Requests\ProductInsertRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\VariantCreateRequest;
use App\Models\NewestVariant;
use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;
use App\StateMachine\Enums\ProductStatus;
use App\StateMachine\States\BaseState;
use Illuminate\Http\Request;

class ProductService extends BaseService implements ProductServiceInterface
{
    public function __construct(protected VariantService $variantService)
    {

    }
    public function addFilter($searchObject, $query)
    {
        if ($searchObject->name) {
            $query = $query->where(function ($query) use ($searchObject) {
                $query->orWhere('name', 'ILIKE', '%' . $searchObject->name . '%');
                $query->orWhereRaw("to_tsvector('english', name) @@ to_tsquery(?)", [$searchObject->name]);
            });
        }

        if ($searchObject->validFrom) {
            $query = $query->where('validFrom', '>=', $searchObject->validFrom);
        }

        if ($searchObject->validTo) {
            $query = $query->where('validTo', '<=', $searchObject->validTo);
        }

        if ($searchObject->priceGT || $searchObject->priceLT) {
            $query = $this->applyPriceFilter($query, $searchObject);
        }

        return $query;
    }

    private function applyPriceFilter($query, $searchObject)
    {
        return $query->with(['variants' => function ($variantQuery) use ($searchObject) {
            $this->addPriceConditions($variantQuery, $searchObject);
        }])
            ->whereHas('variants', function ($variantQuery) use ($searchObject) {
                $this->addPriceConditions($variantQuery, $searchObject);
            });
    }

    private function addPriceConditions($query, $searchObject)
    {
        if ($searchObject->priceGT) {
            $query->where('price', '>=', $searchObject->priceGT);
        }
        if ($searchObject->priceLT) {
            $query->where('price', '<', $searchObject->priceLT);
        }
    }


    public function includeRelation($searchObject, $query)
    {
        if ($searchObject->includeProductType) {
            $query = $query->with('productType');
        }

        if ($searchObject->includeVariants) {
            $query = $query->with('variants');
        }

        return $query;
    }


    protected function getModelClass()
    {
        return new Product();
    }

    public function getInsertRequestClass()
    {
        return ProductInsertRequest::class;
    }

    public function getUpdateRequestClass()
    {
        return ProductUpdateRequest::class;
    }

    public function add($request)
    {
        $state = BaseState::createState(ProductStatus::DRAFT->value);

        return $state->addProduct($request->all());
    }

    public function insert($request)
    {
        $model = Product::create($request);
        return $model;
    }

    public function addVariant(VariantCreateRequest $request)
    {
        $model = Product::find($request['product_id']);

        $state = BaseState::createState($model->status);

        return $state->addVariant($request);
    }

    public function activate($id, ActivateRequest $request)
    {
        $model = Product::find($id);

        $state = BaseState::createState($model->status);

        return $state->activate($request, $model);
    }

    public function hideProduct(int $id)
    {
        $product = Product::find($id);

        $state = BaseState::createState($product->status);

        return $state->hideProduct($product);
    }

    public function draftProduct(int $id)
    {
        $model = Product::find($id);
        $state = BaseState::createState($model->status);
        return $state->productDraft($model);
    }

//    public function update(Request $request, int $id)
//    {
//        $model = Product::find($id);
//
//        if(!$model)
//        {
//            throw new UserException("Resource not found!");
//        }
//
//        $state = BaseState::createState($model->status);
//
//        return $state->updateProduct($id, $request);
//    }

    public function update($request, int $id)
    {
        $model = Product::find($id);

        if(!$model)
        {
            throw new UserException("Resource not found!");
        }

        $model->update($request->all());
        return $model;
    }

    public function allowedActions(int $id)
    {
        $model = Product::find($id);

        if(!$model)
        {
            throw new UserException("Resource not found!");
        }

        $state = BaseState::createState($model->status);

        return $state->allowedActions($id);
    }

    public function getNewestVariants()
    {
        return NewestVariant::withNewestVariant();
    }
}
