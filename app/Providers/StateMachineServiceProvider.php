<?php

namespace App\Providers;

use App\Services\ProductService;
use App\Services\VariantService;
use App\StateMachine\States\ActiveState;
use App\StateMachine\States\DeleteState;
use App\StateMachine\States\DraftState;
use Illuminate\Support\ServiceProvider;

class StateMachineServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('ActiveState', function ($app) {
            return new ActiveState(
                $app->make(ProductService::class),
                $app->make(VariantService::class)
            );
        });

        $this->app->bind('DraftState', function ($app) {
            return new DraftState(
                $app->make(ProductService::class),
                $app->make(VariantService::class)
            );
        });

        $this->app->bind('DeleteState', function ($app) {
            return new DeleteState(
                $app->make(ProductService::class),
                $app->make(VariantService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
