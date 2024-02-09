<?php

namespace App\Services\Interfaces;

use App\Http\Requests\ActivateRequest;
use App\Http\Requests\SearchObjects\BaseSearchObject;

interface ProductServiceInterface extends BaseServiceInterface
{
    public function activate($id, ActivateRequest $request);
    public function hideProduct(int $id);
    public function draftProduct(int $id);
    public function allowedActions(int $id);
    public function getNewestVariants(BaseSearchObject $searchObject);
}
