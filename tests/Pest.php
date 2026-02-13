<?php

use Omisai\Szamlazzhu\Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class)->in(__DIR__);

function skipIfConfigNotSet(string $key)
{
    if (config($key) === null) {
        test()->markTestSkipped("Config key {$key} not set");
    }
}
