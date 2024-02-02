<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Http\Requests\SearchObjects\BaseSearchObject;
use App\Http\Requests\SearchObjects\ProductSearchObject;
use App\Services\Interfaces\BaseServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
abstract class BaseService implements BaseServiceInterface
{
    abstract protected function getModelClass();


    public function getPageable($searchObject)
    {
        $query = $this->getModelClass()->query();

        $query = $this->includeRelation($searchObject, $query);
        $query = $this->addFilter($searchObject, $query);

        return $query->paginate($searchObject->size);
    }


    public function getById(int $id, $searchObject)
    {
        $query = $this->getModelClass()->query();

        $query = $this->includeRelation($searchObject, $query);
        $query = $this->addFilter($searchObject, $query);

        $result = $query->find($id);

        if(!$result)
        {
            throw new UserException("Resource not found!");
        }

        return $result;
    }

    public function add(array $request)
    {
        return $this->getModelInstance()->create($request);
    }

    public function update(array $request, int $id)
    {
        $model = $this->getModelInstance()->find($id);

        if(!$model)
        {
            throw new UserException("Resource not found!");
        }

        $model->update($request);

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

    public function addFilter($searchObject, $query){
        return $query;
    }

    public function getSearchObject($params)
    {
        return new BaseSearchObject($params);
    }

    public function includeRelation($searchObject, $query)
    {
        return $query;
    }

    protected function getModelInstance(): Model
    {
        $modelClass = $this->getModelClass();

        return new $modelClass;
    }

}
