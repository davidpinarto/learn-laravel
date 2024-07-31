<?php

namespace App\Providers;

use App\Data\Bar;
use App\Data\Foo;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * DeferrableProvider
 * berfugnsi untuk menandai sebuah Service Provider agar tidak di load jika 
 * tidak dibutuhkan dependency nya
 */
class FooBarServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->app->singleton(Foo::class, function () {
            return new Foo();
        });
        $this->app->singleton(Bar::class, function ($app) {
            return new Bar($app->make(Foo::class));
        });
    }

    public function boot()
    {
        //
    }

    /**
     * DeferrableProvider definisikan disini class yang tidak di load jika tidak di butuhkan
     */
    public function provides()
    {
        return [Foo::class, Bar::class];
    }
}
