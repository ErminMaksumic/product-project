<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface BaseServiceInterface
{
    public function getPageable($searchObject);
    public function getById(int $id, $searchObject);
    public function add(Request $request);
    public function update(Request $request, int $id);
    public function remove(int $id);
    public function upload($request, $processJob);
    public function batchProgress($request,$batch_id);
}
