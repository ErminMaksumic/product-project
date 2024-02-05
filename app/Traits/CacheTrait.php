<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheTrait
{
    protected function getCachedData($key, $minutes, $callback)
    {
        if ($key != 'none') {
            $cacheKey = $key . '_' . md5(json_encode(request()->all()));

            $cachedData = Cache::get($cacheKey);

            if ($cachedData) {
                return $cachedData;
            }


            $data = $callback();

            Cache::put($cacheKey, $data, $minutes);


            return $data;
        }

        return $callback();
    }

    public function forgetCachedData($key)
    {
        $cacheKey = $key . '_' . md5(json_encode(request()->all()));

        Cache::forget($cacheKey);
    }
}
