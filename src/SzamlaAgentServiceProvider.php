<?php

namespace Omisai\Szamlazzhu;

use Illuminate\Support\ServiceProvider;

class SzamlaAgentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->offerPublishing();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/szamlazzhu.php',
            'szamlazzhu'
        );

        $this->app->extend('config', function ($config) {
            $config['filesystems.disks.payment'] = [
                'driver' => 'local',
                'root' => storage_path('app/szamlazzhu'),
                'throw' => false,
                'visibility' => 'private',
                'directory_visibility' => 'private',
            ];

            return $config;
        });

        $this->app->extend('config', function ($config) {
            $config['logging.channels.szamlazzhu'] = [
                'driver' => 'daily',
                'path' => storage_path(sprintf('logs/%s.log', env('SZAMLAZZHU_LOG_FILENAME', 'szamlazzhu'))),
                'level' => env('SZAMLAZZHU_LOG_LEVEL', 'warning'),
                'days' => 30,
            ];

            return $config;
        });
    }

    protected function offerPublishing(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        if (!function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([
            __DIR__.'/../config/szamlazzhu.php' => config_path('szamlazzhu.php'),
        ], 'szamlazzhu-config');
    }
}
