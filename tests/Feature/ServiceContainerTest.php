<?php

namespace Tests\Feature;

use App\Data\Bar;
use App\Data\Foo;
use App\Data\Person;
use App\Services\HelloServices;
use App\Services\HelloServicesIndonesia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceContainerTest extends TestCase
{
    public function testDependencyInjection()
    {
        $foo1 = $this->app->make(Foo::class); // new Foo()
        $foo2 = $this->app->make(Foo::class); // new Foo()

        self::assertEquals('foo', $foo1->foo());
        self::assertEquals('foo', $foo2->foo());
        self::assertNotSame($foo1, $foo2);
    }

    public function testBind()
    {
        // untuk pembuatan object yang kompleks kita bisa menggyunakan method bind milik app (service container / application class)
        $this->app->bind(Person::class, function ($app) {
            return new Person('david', 'pinarto', 23);
        });

        $person1 = $this->app->make(Person::class); // closure // new Person('david','pinarto',23)
        $person2 = $this->app->make(Person::class); // closure // new Person('david','pinarto',23)

        self::assertEquals('david', $person1->firstname);
        self::assertEquals('pinarto', $person1->lastname);
        self::assertEquals(23, $person1->age);

        self::assertEquals('david', $person2->firstname);
        self::assertEquals('pinarto', $person2->lastname);
        self::assertEquals(23, $person2->age);

        self::assertNotSame($person1, $person2);
    }

    public function testSingleton()
    {
        // untuk pembuatan object yang kompleks kita bisa menggyunakan method bind milik app (service container / application class)
        $this->app->singleton(Person::class, function ($app) {
            return new Person('david', 'pinarto', 23);
        });

        $person1 = $this->app->make(Person::class); // closure // new Person('david','pinarto',23) if not exist
        $person2 = $this->app->make(Person::class); // return existing

        self::assertEquals('david', $person1->firstname);
        self::assertEquals('pinarto', $person1->lastname);
        self::assertEquals(23, $person1->age);

        self::assertEquals('david', $person2->firstname);
        self::assertEquals('pinarto', $person2->lastname);
        self::assertEquals(23, $person2->age);

        self::assertSame($person1, $person2);
    }

    public function testInstance()
    {
        // mirip dengan singleton, hanya saja instance menggunakan referensi dari object yang sudah ada
        $person = new Person('david', 'pinarto', 23);
        $this->app->instance(Person::class, $person);

        $person1 = $this->app->make(Person::class); // person
        $person2 = $this->app->make(Person::class); // person
        $person3 = $this->app->make(Person::class); // person
        $person4 = $this->app->make(Person::class); // person

        self::assertEquals('david', $person1->firstname);
        self::assertEquals('pinarto', $person1->lastname);
        self::assertEquals(23, $person1->age);

        self::assertEquals('david', $person2->firstname);
        self::assertEquals('pinarto', $person2->lastname);
        self::assertEquals(23, $person2->age);

        self::assertSame($person1, $person2);
    }

    public function testDependencyInjectionWithServiceContainer()
    {
        /**
         * Secara default, jika kita membuat object menggunakan make(key), lalu Laravel mendeteksi terdapat constructor, maka Laravel akan mencoba menggunakan dependency yang sesuai dengan tipe   yang dibutuhkan di Laravel
         */
        $this->app->singleton(Foo::class, function ($app) {
            return new Foo();
        });

        /**
         * jadi service container bisa melakukan dependency injection secara otomatis jika 
         * function __construct di definisikan pada sebuah class, dan tentunya kita harus membuat object 
         * yang di butuhkan menggunakan service container yaitu Foo agar service container dapat melakukan 
         * dependency injection secara otomatis pada object Bar
         * 
         * misalnya kita membuat singleton object Foo() menggunakan service worker
         * 
         * $foo = $this->app->make(Foo::class);
         * 
         * dan kita membuat object bar yang menggunakan object Foo sebagai constructornya
         * 
         * nanti secara otomatis service container akan mengenali dan melakukan dependency injection 
         * secara otomatis ke $bar, dan nilai property pada object $bar pun akan bernilai foo
         */
        $foo = $this->app->make(Foo::class);
        $bar = $this->app->make(Bar::class);

        /**
         * method bar() pada object $bar berhasil memanggil method foo() yang return nilai 'foo'
         * dan membuat nilai return dari method bar() menjadi foo and bar
         * 
         * artinya dependency injection terjadi secara automatic karena menggunakan service container ini
         * 
         */
        self::assertEquals('foo and bar', $bar->bar());
        self::assertSame($foo, $bar->foo);
    }

    public function testDependencyInjectionInClosure()
    {
        $this->app->singleton(Foo::class, function ($app) {
            return new Foo();
        });
        /**
         * jika kita ingin membuat $bar mengjadi singleton juga sedangkan kita ingin melakukan dependency
         * injection yg dimana $bar membutuhkan $foo, sedangkan kita sudah membuat singleton $foo di 
         * service container maka kita bisa menggunakan argument $app pada closure, yang dimana 
         * argument $app ini merupakan service container juga
         * 
         * kita bisa melakukannya seperti kode di bawah ini untuk melakukan dependency injection dari $foo
         * ke $bar
         */
        $this->app->singleton(Bar::class, function ($app) {
            return new Bar($this->app->make(Foo::class));
        });

        $foo = $this->app->make(Foo::class);
        $bar1 = $this->app->make(Bar::class);
        $bar2 = $this->app->make(Bar::class);

        self::assertSame($foo, $bar1->foo);
        self::assertSame($bar1, $bar2);
    }

    public function testHelloServices() {
        /**
         * Interface adalah sebuah kontrak untuk sebuah Class
         * maksudnya adalah sebuah class wajib mengimplementasikan function yang di definisikan
         * pada Interface
         */

        /**
         * kode ini bermaksud untuk memberitau ketika kita ingin membuat singleton menggunakan interface
         * HelloServicse, maka buatkan object dari class HelloServicesIndonesia
         */
        $this->app->singleton(HelloServices::class, HelloServicesIndonesia::class);

        /**
         * dan ketika kode di atas tereksekusi, maka services container akan membuat class 
         * HelloServicesIndonesia menggunakan Interface HelloServices
         */
        $helloService = $this->app->make(HelloServices::class);
        self::assertEquals("Halo david", $helloService->hello('david'));

        /**
         * Rangkuman dari Binding Interface ke Class
         * Interface: mendefinisikan sebuah kontrak pada class agar sebuah class wajib mengikuti
         * apa yang di definisikan pada sebuah interface
         * 
         * Binding: menghubungkan dan mengimplementasikan interface (HelloServices) pada class
         * (HelloServicesIndonesia)
         * 
         * Flexibel: dengan menggunakan binding kita bisa dengan mudah mengganti implementasi dari 
         * HelloServices tanpa mengubah kode yang menggunakan HelloServices, 
         * 
         * Misalnya, kita bisa mengganti HelloServicesIndonesia dengan HelloServicesEnglish
         * atau implementasi lainnya tanpa perlu mengubah kode yang memanggil HelloServices
         */
    }
}
