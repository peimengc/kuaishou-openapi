<?php


namespace Peimengc\KuaishouOpenapi;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Openapi::class, function () {
            return new Openapi(config('services.kuaishou-openapi.appid'), config('services.kuaishou-openapi.secret'));
        });

        $this->app->alias(Openapi::class, 'kuaishou-openapi');
    }

    public function provides()
    {
        return [Openapi::class, 'kuaishou-openapi'];
    }
}