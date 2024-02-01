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

    protected function handleDeleteResponse()
    {
        return response(content: "Resource removed successfully", status: 204);
    }

    public function getPageable($searchObject)
    {
        $query = $this->getModelClass()->query();

        $query = $this->includeRelation($searchObject, $query);
        $query = $this->addFilter($searchObject, $query);

        return $query->paginate($searchObject->size);
    }


    public function getById(int $id)
    {
        $searchObjectInstance = $this->getSearchObject(request()->query());
        $query = $this->getModelClass()->query();

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

    public function addFilter(BaseSearchObject $searchObject, $query){
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
