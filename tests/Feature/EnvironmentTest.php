<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Env;
use Tests\TestCase;

class EnvironmentTest extends TestCase
{
    public function testGetEnv()
    {
        $myName = env('MY_NAME');
        $myFullName = env('MY_FULL_NAME');

        self::assertEquals('david', $myName);
        self::assertEquals('david pinarto', $myFullName);
    }

    public function testDefaultValue()
    {
        $myAge = env('MY_AGE', 23);
        $myLastName = Env::get('MY_LAST_NAME','pinarto');

        self::assertEquals(23, $myAge);
        self::assertEquals('pinarto', $myLastName);
    }
}
