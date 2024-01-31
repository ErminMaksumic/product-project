<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Http\Requests\SearchObjects\BaseSearchObject;
use App\Services\Interfaces\BaseServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BaseService implements BaseServiceInterface
{
    abstract protected function getModelClass();

    protected function handleDeleteResponse()
    {
        return response(content: "Resource removed successfully", status: 204);
    }

    public function getPageable()
    {
        $searchObjectInstance = $this->getSearchObject();
        $searchObjectInstance->fill(request()->query());

        $query = $this->getModelClass()->query();

        $query = $this->includeRelation($searchObjectInstance, $query);
        $query = $this->addFilter($searchObjectInstance, $query);

        return $query->paginate($searchObjectInstance->size);
    }


    public function getById(int $id)
    {
        $searchObjectInstance = $this->getSearchObject();
        $query = $this->getModelClass()->query();

        $searchObjectInstance->fill(request()->query());
        $query = $this->includeRelation($searchObjectInstance, $query);
        $query = $this->addFilter($searchObjectInstance, $query);

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

        if (!$model) {
            abort(404, 'Resource not found');
        }

        $model->update($request);

        return $model;
    }

    public function remove(int $id)
    {
        $model = $this->getModelInstance()->find($id);

        if(!$model)
        {
            abort(404, "Resource not found");
        }

        $model->delete();
        return $this->handleDeleteResponse();
    }

    public function addFilter($searchObject, $query){
        return $query;
    }

    public function getSearchObject()
    {
        return new BaseSearchObject();
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
