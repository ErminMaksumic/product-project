<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchObjects\BaseSearchObject;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    public function __construct(protected $service)
    {
        $this->middleware('auth:sanctum')->except(['index']);
    }

    abstract function getInsertRequestClass();
    abstract function getUpdateRequestClass();
    abstract function createResourcePayload($request, $collection = false);

    public function index(Request $request)
    {
        $searchObject = $request->attributes->get('searchObject');
        return $this->createResourcePayload($this->service->getPageable($searchObject), true);
    }

    public function store(Request $request)
    {
        $this->authorize('admin');
        $validatedData = $this->validateRequest($request, $this->getInsertRequestClass());
        return $this->createResourcePayload($this->service->add($validatedData));
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
        return $this->createResourcePayload($this->service->getById($id));
    }

    public function update(Request $request, int $id)
    {
        $this->authorize('admin');
        $validatedData = $this->validateRequest($request, $this->getUpdateRequestClass());
        return $this->createResourcePayload($this->service->update($validatedData, $id));
    }

    public function destroy(int $id)
    {


        $this->service->remove($id);
    }
}
