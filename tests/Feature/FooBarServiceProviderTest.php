<?php

namespace Tests\Feature;

use App\Data\Bar;
use App\Data\Foo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FooBarServiceProviderTest extends TestCase
{
    public function testFooBarServiceProvider()
    {
        /**
         * ini adalah singleton sesuai yang di registrasikan melalui services provider 
         * jadi seharusnya nilainya sama karena foo dan bar adalah singleton
         */
        $foo1 = $this->app->make(Foo::class);
        $foo2 = $this->app->make(Foo::class);

        self::assertSame($foo1, $foo2);

        $bar1 = $this->app->make(Bar::class);
        $bar2 = $this->app->make(Bar::class);

        self::assertSame($bar1, $bar2);
        self::assertSame($foo1, $bar1->foo);
    }

    public function testEmpty()
    {
        /**
         * harus hati hati ketika kita ingin menggunakan fungsi deferredProviders, pastikan cache services 
         * di clear terlebih dahulu agar dapat memperbarui services provider dan services container, jika 
         * tidak di clear, maka terkadang walaupun kita sudah menggunaakn implements deferredProviders, 
         * pas di compile oleh laravel malah yang di cachenya tersebut, sehingga defferredProvider tidak 
         * berfungsi dengan seharusnya
         * 
         * php artisan clear-compiled
         */
        self::assertTrue(true);
    }
}
