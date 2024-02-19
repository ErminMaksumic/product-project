<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Http\Requests\ActivateRequest;
use App\Http\Requests\ProductInsertRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\VariantCreateRequest;
use App\Jobs\ProductCsvProcess;
use App\Models\NewestVariant;
use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;
use App\StateMachine\Enums\ProductStatus;
use App\StateMachine\States\BaseState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use PHPJasper\PHPJasper;

class ProductService extends BaseService implements ProductServiceInterface
{

    protected $jasper;

    public function __construct(protected VariantService $variantService)
    {
        $this->jasper = new PHPJasper();
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

        if (!$model) {
            throw new UserException("Resource not found!");
        }

        $model->update($request->all());
        return $model;
    }

    public function allowedActions(int $id)
    {
        $model = Product::find($id);

        if (!$model) {
            throw new UserException("Resource not found!");
        }

        $state = BaseState::createState($model->status);

        return $state->allowedActions($id);
    }

    public function getNewestVariants()
    {
        return NewestVariant::withNewestVariant();
    }


    public function generateReportForOneProduct($request, int $id)
    {
        $parameters = [
            'productId' => $id
        ];
        $fileName = 'Product-variants';

        return parent::generateReport($parameters, $fileName, $request);
    }

    public function generateReportForExpensiveProducts($request)
    {
        $imagePath = __DIR__ . '\Reports\Template\Assets\cherry.jpg';
        $parameters = [
            'price' => 9900,
            'IMAGE_PATH' => $imagePath,
        ];
        $fileName = 'Expensive_products';

        return parent::generateReport($parameters, $fileName, $request);
    }

    public function generateReportForProductStatesGraph($request)
    {
        $parameters = [];
        $fileName = 'Chart';

        return parent::generateReport($parameters, $fileName, $request);
    }


    public function upload(Request $request)
    {
        Log::info($request);

        $stream = fopen('php://input', 'r');

        if ($stream) {
            fseek($stream, 0);

            $batchSize = 10000;
            $filePath = storage_path('app/uploads/tmp.csv');
            $headerSkipped = false;
            $rowCount = 0;

            try {
                while (!feof($stream)) {
                    $batch = '';
                    while ($rowCount < $batchSize && !feof($stream)) {
                        if (!$headerSkipped) {
                            fgets($stream);
                            $headerSkipped = true;
                            continue;
                        }
                        $batch .= fgets($stream);
                        $rowCount++;
                    }

                    file_put_contents($filePath, $batch);

                    Queue::push(new ProductCsvProcess($filePath));

                    $rowCount = 0;
                }
                fclose($stream);

                return response()->json(['message' => 'File upload processing started']);
            } catch (\Exception $e) {
                // Log the error
                Log::error("Error processing file upload: " . $e->getMessage());

                // Return a 500 error response
                return Response::json(['error' => 'File upload processing failed'], 500);
            }
        } else {
            return response()->json(['error' => 'Failed to open the stream'], 500);
        }
    }
}
