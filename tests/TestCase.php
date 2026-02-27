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
            $envPath = __DIR__.'/..';
            if (file_exists($envPath.'/.env') || file_exists($envPath.'/.env.testing')) {
                $app->useEnvironmentPath($envPath);
                $app->make('Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables')->bootstrap($app);
            }
        }
    }
}
