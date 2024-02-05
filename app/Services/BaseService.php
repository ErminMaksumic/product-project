<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Http\Requests\SearchObjects\BaseSearchObject;
use App\Http\Requests\SearchObjects\ProductSearchObject;
use App\Services\Interfaces\BaseServiceInterface;
use App\Traits\CacheTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class BaseService implements BaseServiceInterface
{
    use CacheTrait;

    abstract protected function getModelClass();
    abstract protected function getCachedName($key);
    abstract function getInsertRequestClass();
    abstract function getUpdateRequestClass();


    public function getPageable($searchObject)
    {
        return $this->getCachedData($this->getCachedName('getPageable'), 60, function () use ($searchObject) {
            $query = $this->getModelClass()->query();

            $query = $this->includeRelation($searchObject, $query);
            $query = $this->addFilter($searchObject, $query);

            return $query->paginate($searchObject->size);
        });
    }


    public function getById(int $id, $searchObject)
    {
        return $this->getCachedData($this->getCachedName('getById'), 60, function () use ($id, $searchObject) {
            $query = $this->getModelClass()->query();
            $query = $this->includeRelation($searchObject, $query);
            $result = $query->find($id);

            if (!$result) {
                throw new UserException("Resource not found!");
            }

            return $result;
        });
    }

    public function add(Request $request)
    {
        $this->validateRequest($request, $this->getInsertRequestClass());
        $this->forgetCachedData($this->getCachedName('getPageable'));

        return $this->getModelInstance()->create($request->all());
    }

    public function update(Request $request, int $id)
    {
        $this->validateRequest($request, $this->getUpdateRequestClass());
        $model = $this->getModelInstance()->find($id);

        if (!$model) {
            throw new UserException("Resource not found!");
        }

        $model->update($request->all());


        $this->forgetCachedData($this->getCachedName('getPageable'));
        $this->forgetCachedData($this->getCachedName('getById'));

        return $model;
    }

    public function remove(int $id)
    {
        $model = $this->getModelInstance()->find($id);

        if (!$model) {
            throw new UserException("Resource not found!");
        }

        $model->delete();
        $this->forgetCachedData($this->getCachedName('getPageable'));

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
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }
}
