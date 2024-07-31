<?php

namespace App\Services;

class HelloServicesIndonesia implements HelloServices
{
    public function hello(string $name): string
    {
        return "Halo $name";
    }
}
