<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Http\Requests\SearchObjects\BaseSearchObject;
use App\Http\Requests\SearchObjects\ProductSearchObject;
use App\Services\Interfaces\BaseServiceInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

        if($searchObject->size > 100)
            $searchObject->size = 10;

        return $query->paginate($searchObject->size);
    }


    public function getById(int $id, $searchObject)
    {
        $query = $this->getModelClass()->query();
        $query = $this->includeRelation($searchObject, $query);
        $result = $query->find($id);

        if(!$result)
        {
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
    }

    public function update(Request $request, int $id)
    {
        $this->validateRequest($request, $this->getUpdateRequestClass());
        $model = $this->getModelInstance()->find($id);

        if(!$model)
        {
            throw new UserException("Resource not found!");
        }

        $model->update($request->all());

        return $model;
    }

    public function remove(int $id)
    {
        $model = $this->getModelInstance()->find($id);

        if(!$model)
        {
            throw new UserException("Resource not found!");
        }

        $model->delete();

        return $model;
    }

    public function addFilter($searchObject, Builder $query){
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

}
