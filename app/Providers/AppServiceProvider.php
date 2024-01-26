<?php

namespace App\Providers;

use App\Services\Interfaces\ProductServiceInterface;
use App\Services\Interfaces\ProductTypeServiceInterface;
use App\Services\Interfaces\VariantServiceInterface;
use App\Services\ProductService;
use App\Services\ProductTypeService;
use App\Services\VariantService;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
