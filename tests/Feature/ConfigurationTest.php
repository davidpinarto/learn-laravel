<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConfigurationTest extends TestCase
{
    public function testConfiguration()
    {
        $firstName = config('contoh.names.firstName');
        $lastName = config('contoh.names.lastName');
        $age = config('contoh.age');
        $email = config('contoh.email');

        self::assertEquals('david', $firstName);
        self::assertEquals('pinarto', $lastName);
        self::assertEquals(23, $age);
        self::assertEquals('davidpinarto90@gmail.com', $email);
    }
}
