<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
//        $this->registerPolicies();
//        return Gate::define('admin', function ($user) {
//            $role = $user->relationLoaded('role') ? $user->role : $user->load('role')->role;
//            return $role && $role->name === 'admin';
//        });

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        
        Passport::tokensCan([
            'product-types' => 'Product types resources',
            'products' => 'Products resources',
        ]);

    }

}
