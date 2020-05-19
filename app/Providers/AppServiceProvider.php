<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\RouteRegistrar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Passport::routes(function (RouteRegistrar $router) {
            $router->forAccessTokens();
        }, ['middleware' => 'passport']);
        // access_token 过期时间
        Passport::tokensExpireIn(now()->addDays(15));
        // refreshTokens 过期时间
        Passport::refreshTokensExpireIn(now()->addDays(30));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        if ($this->app->environment() !== 'production') {
//            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
//        }
    }
}
