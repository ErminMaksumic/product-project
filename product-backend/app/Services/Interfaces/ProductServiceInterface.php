<?php

namespace App\Services\Interfaces;

use App\Http\Requests\ActivateRequest;
use Illuminate\Http\Request;

interface ProductServiceInterface extends BaseServiceInterface
{
    public function activate($id, ActivateRequest $request);
    public function hideProduct(int $id);
    public function draftProduct(int $id);
    public function allowedActions(int $id);
    public function getNewestVariants();
    public function generateReportForOneProduct($request, int $id);
    public function generateReportForExpensiveProducts($request);
    public function generateReportForProductStatesGraph($request);
    public function upload(Request $request);
}
