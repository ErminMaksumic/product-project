<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Http\Requests\ActivateRequest;
use App\Http\Requests\SearchObjects\ProductSearchObject;
use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;
use App\StateMachine\Enums\ProductStatus;
use App\StateMachine\States\BaseState;

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

    public function getSearchObject()
    {
        return new ProductSearchObject();
    }

    protected function getModelClass()
    {
        return new Product();
    }

    public function add($request)
    {
        $state = BaseState::createState(ProductStatus::DRAFT->value);

        return $state->addProduct($request);
    }

    public function addVariant(array $request)
    {
        $model = Product::find($request['product_id']);

        $state = BaseState::createState($model->status);

        return $state->addVariant($request);
    }

    public function activate($id, array $request)
    {
        $model = Product::find($id);

        $state = BaseState::createState($model->status);

        return $state->productActivate($request, $id);
    }

    public function hideProduct($id)
    {
        $model = Product::find($id);

        $state = BaseState::createState($model->status);

        return $state->hideProduct($id);
    }

    public function update(array $request, int $id)
    {
        $model = Product::find($id);

        if (!$model) {
            abort(404, 'Resource not found');
        }

        $state = BaseState::createState($model->status);

        return $state->updateProduct($id, $request);
    }

    public function insertVariant($request)
    {
        $state = BaseState::createState(ProductStatus::DRAFT);

        return $state->update($request);
    }

    public function updateVariant($request)
    {
        $state = BaseState::createState(ProductStatus::ACTIVATED);

        return $state->update($request);
    }
}
