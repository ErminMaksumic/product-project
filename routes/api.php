<?php

use App\Http\Controllers\ApiControllers\ProductController;
use App\Http\Controllers\ApiControllers\ProductTypeController;
use App\Http\Controllers\ApiControllers\VariantController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/product/fullTextSearch', [ProductController::class, 'fullTextSearch']);
Route::get('/product/newestVariant', [ProductController::class, 'newestVariant']);
Route::apiResource('product', ProductController::class);
Route::apiResource('productType', ProductTypeController::class);
Route::apiResource('variant', VariantController::class);

// auth
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware("auth:sanctum");

// state machine

Route::get('/product/{id}/allowedActions', [ProductController::class, 'allowedActions'])->name('product.allowedActions');
Route::put('/product/{id}/productActivate', [ProductController::class, 'DraftToActive'])->name('product.productActivate');
Route::put('/product/{id}/productDraft', [ProductController::class, 'productDraft'])->name('product.productDraft');
Route::put('/product/{id}/productDelete', [ProductController::class, 'ActiveToDeleted'])->name('product.productDelete');
