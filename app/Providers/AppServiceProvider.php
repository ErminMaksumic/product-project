<?php

namespace App\Providers;

use App\Exceptions\CustomExceptionHandler;
use App\Services\Interfaces\ProductServiceInterface;
use App\Services\Interfaces\ProductTypeServiceInterface;
use App\Services\Interfaces\VariantServiceInterface;
use App\Services\ProductService;
use App\Services\ProductTypeService;
use App\Services\VariantService;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(ProductTypeServiceInterface::class, ProductTypeService::class);
        $this->app->bind(VariantServiceInterface::class, VariantService::class);

        // exception handler

//        $this->app->singleton(
//            ExceptionHandler::class,
//            CustomExceptionHandler::class
//        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
