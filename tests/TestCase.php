<?php

namespace Omisai\Szamlazzhu\Tests;

use Omisai\Szamlazzhu\SzamlaAgentServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected $loadEnvironmentVariables = true;

    protected function getPackageProviders($app)
    {
        return [
            SzamlaAgentServiceProvider::class,
        ];
    }

    /**
     * Resolve application core environment variables implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationEnvironmentVariables($app)
    {
        if (property_exists($this, 'loadEnvironmentVariables') && $this->loadEnvironmentVariables === true) {
            $app->useEnvironmentPath(__DIR__.'/..');
            $app->make('Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables')->bootstrap($app);
        }
    }
}
