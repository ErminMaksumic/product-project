<?php

use App\Http\Controllers\ApiControllers\ProductController;
use App\Http\Controllers\ApiControllers\ProductTypeController;
use App\Http\Controllers\ApiControllers\VariantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaseController;
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


// Custom path
Route::get('/product/newestVariant', [ProductController::class, 'getNewestVariant']);
Route::post('/product/variant', [ProductController::class, 'addVariant']);
Route::post('/product/{id}/generateReport', [ProductController::class, 'generateReportForOneProduct']);
Route::post('/product/generateReport', [ProductController::class, 'generateReportForExpensiveProducts']);
Route::post('/product/generateReportChart', [ProductController::class, 'generateReportForProductStatesGraph']);
Route::get('/download', [ProductController::class, 'download'])->name('download');

Route::middleware(['auth:api', 'scopes:product-types'])->group(function () {
    Route::apiResource('productType', ProductTypeController::class);
});

Route::middleware(['auth:api', 'scopes:products'])->group(function () {
    Route::apiResource('product', ProductController::class);
});


// Resources
Route::apiResource('variant', VariantController::class);

// Auth
Route::post('login', [AuthController::class, 'login']);
//Route::post('logout', [AuthController::class, 'logout'])->middleware("auth:sanctum");
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// State machine
Route::get('/product/{id}/allowedActions', [ProductController::class, 'allowedActions'])->name('product.allowedActions');
Route::put('/product/{id}/productActivate', [ProductController::class, 'productActivate'])->name('product.productActivate');
Route::put('/product/{id}/productDraft', [ProductController::class, 'productDraft'])->name('product.productDraft');
Route::put('/product/{id}/productDelete', [ProductController::class, 'productHide'])->name('product.productHide');

Route::get('/redirect', function (Request $request) {
    $query = http_build_query([
        'client_id' => '1',
        'redirect_uri' => 'http://localhost:3000/callback',
        'response_type' => 'code',

    ]);
    return redirect('http://localhost:8000/oauth/authorize?'.$query);
});
