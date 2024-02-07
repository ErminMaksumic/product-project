<?php

namespace App\Services\Interfaces;

use App\Http\Requests\ActivateRequest;

interface ProductServiceInterface extends BaseServiceInterface
{
    public function activate($id, ActivateRequest $request);
    public function hideProduct(int $id);
    public function draftProduct(int $id);
    public function allowedActions(int $id);
    public function getNewestVariants();
    public function getProductSumPrice();
}
