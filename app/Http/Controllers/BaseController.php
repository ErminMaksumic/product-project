<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class BaseController extends Controller
{
    public function __construct(protected $service)
    {
        $this->middleware('auth:sanctum')->except(['index', 'generateReportForOneProduct', 'generateReportForExpensiveProducts', 'generateReportForProductStatesGraph', 'download']);
    }

    abstract function getInsertRequestClass();
    abstract function getUpdateRequestClass();
    abstract function getSearchObject($params);

    abstract function createResourcePayload($request, bool $collection = false);

    public function index()
    {
        $searchObject = $this->getSearchObject(request()->query());
        return $this->createResourcePayload($this->service->getPageable($searchObject), true);
    }

    public function store(Request $request)
    {
        $this->authorize('admin');
        $this->validateRequest($request, $this->getInsertRequestClass());
        return $this->createResourcePayload($this->service->add($request));
    }

    public function validateRequest(Request $request, $formRequest)
    {
        try {
            $formRequestInstance = new $formRequest();
            $validatedData = $this->validate($request, $formRequestInstance->rules());
            return $validatedData;
        } catch (Illuminate\Validation\ValidationException $e) {
            return $this->handleValidationException($e);
        }
    }

    protected function handleValidationException(Illuminate\Validation\ValidationException $e)
    {
        $errors = $e->validator->errors();
        $errorArray = $errors->toArray();

        return response()->json([
            'errors' => $errorArray,
        ], 422);
    }

    public function show(int $id)
    {
        $searchObject = $this->getSearchObject(request()->query());
        return $this->createResourcePayload($this->service->getById($id, $searchObject));
    }

    public function update(Request $request, int $id)
    {
        $this->authorize('admin');
        $this->validateRequest($request, $this->getUpdateRequestClass());
        return $this->createResourcePayload($this->service->update($request, $id));
    }

    public function destroy(int $id)
    {
        $this->service->remove($id);
        return response(content: "Resource removed successfully", status: 204);
    }

    public function download(Request $request)
    {
        $filePath = $request->query('filePath');
        $fileName = basename($filePath);

        if (!$filePath || !file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}
