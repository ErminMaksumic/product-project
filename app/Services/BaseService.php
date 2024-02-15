<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Http\Requests\SearchObjects\BaseSearchObject;
use App\Http\Requests\SearchObjects\ProductSearchObject;
use App\Jobs\ProductCsvProcess;
use App\Services\Interfaces\BaseServiceInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;
use Symfony\Component\Process\Process;

abstract class BaseService implements BaseServiceInterface
{
    abstract protected function getModelClass();
    abstract function getInsertRequestClass();
    abstract function getUpdateRequestClass();


    public function getPageable($searchObject)
    {
        $query = $this->getModelClass()->query();

        $query = $this->includeRelation($searchObject, $query);
        $query = $this->addFilter($searchObject, $query);

        if ($searchObject->size > 100)
            $searchObject->size = 10;

        return $query->paginate($searchObject->size);
    }


    public function getById(int $id, $searchObject)
    {
        $query = $this->getModelClass()->query();
        $query = $this->includeRelation($searchObject, $query);
        $result = $query->find($id);

        if (!$result) {
            throw new UserException("Resource not found!");
        }

        return $result;
    }

    public function add(Request $request)
    {
        DB::beginTransaction();

        try {
            $this->validateRequest($request, $this->getInsertRequestClass());
            $result = $this->getModelInstance()->create($request->all());
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $result;
    }

    public function update(Request $request, int $id)
    {
        $this->validateRequest($request, $this->getUpdateRequestClass());
        $model = $this->getModelInstance()->find($id);

        if (!$model) {
            throw new UserException("Resource not found!");
        }

        $model->update($request->all());

        return $model;
    }

    public function remove(int $id)
    {
        $model = $this->getModelInstance()->find($id);

        if (!$model) {
            throw new UserException("Resource not found!");
        }

        $model->delete();

        return $model;
    }

    public function addFilter($searchObject, Builder $query)
    {
        return $query;
    }

    public function getSearchObject($params)
    {
        return new BaseSearchObject($params);
    }

    public function includeRelation($searchObject, Builder $query)
    {
        return $query;
    }

    protected function getModelInstance(): Model
    {
        $modelClass = $this->getModelClass();

        return new $modelClass;
    }

    public function validateRequest(Request $request, $formRequest)
    {
        $formRequestInstance = new $formRequest();
        $rules = $formRequestInstance->rules();
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new Illuminate\Validation\ValidationException($validator);
        }
        return $validator->validated();
    }

    public function generateReport(array $parameters, string $fileName, $request)
    {
        //Checking and getting formats from request
        $acceptedFormats = [
            'PDF', 'XLS', 'XLSX', 'DOCX', 'PPTX', 'CSV', 'HTML', 'RTF', 'XML',
            'ODT', 'ODS',
        ];

        $jsonBody = $request->getContent();
        $requestData = json_decode($jsonBody, true);
        $formats = $requestData['formats'] ?? [];

        foreach ($formats as $format) {
            if (!in_array(strtoupper($format), $acceptedFormats)) {
                throw new \InvalidArgumentException('Invalid format: ' . $format);
            }
        }

        //Fetching, preparing and creating files
        $jrxmlFile = __DIR__ . '\Reports\Template\\JasperFiles\\' . $fileName . '.jrxml';

        $jasper = new PHPJasper();
        $jasper->compile($jrxmlFile)->execute();

        $jasperFile = __DIR__ . '\Reports\Template\\JasperFiles\\' . $fileName . '.jasper';
        $outputFile = __DIR__ . '\Reports\Output\\' . $fileName;

        $jasperConfig = config('jasper.db_connection');
        $options = [
            'format' => $formats,
            'params' => $parameters,
            'db_connection' => $jasperConfig
        ];


        //Where the magic happens
        $jasper->process(
            $jasperFile,
            $outputFile,
            $options,
        )->execute();


        $filePaths = [];

        foreach ($formats as $format) {
            $outputFilePath = $outputFile . '.' . strtolower($format);

            $filePaths[] = $outputFilePath;
        }


        return [
            'filePaths' => $filePaths,
        ];
    }

    public function upload(Request $request)
    {
        if ($request->file('mycsv')->isValid()) {
            $uploadedFile = $request->file('mycsv');
            $filename = time() . '_' . $uploadedFile->getClientOriginalName();
            $path = $uploadedFile->storeAs('uploads', $filename, 'local');
            $absolutePath = storage_path('app/' . $path);

        DB::beginTransaction();

        try {
            DB::statement("COPY products FROM '$absolutePath' WITH CSV HEADER;");
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        } else {
            return response()->json(['error' => 'Invalid file or upload error.'], 400);
        }
    }

    public function batchProgress($request, $batch_id)
    {
        $batch = Bus::findBatch($batch_id);

        if (!$batch) {
            return response()->json(['error' => 'Batch not found'], 404);
        }

        $progress = $batch->progress();

        return response()->json(['progress' => $progress], 200);
    }
}
