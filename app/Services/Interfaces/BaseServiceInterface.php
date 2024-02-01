<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface BaseServiceInterface
{
    public function getPageable($searchObject);
    public function getById(int $id, $searchObject);
    public function add(array $request);
    public function update(array $request, int $id);
    public function remove(int $id);
}
