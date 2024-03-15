<?php

namespace App\Providers;

use App\Kafka\Consumer;
use App\Kafka\Producer;
use Illuminate\Support\ServiceProvider;

class KafkaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Consumer::class, function () {
            return new Consumer();
        });

        $this->app->singleton(Producer::class, function () {
            return new Producer();
        });
    }
}
