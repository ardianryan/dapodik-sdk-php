<?php

namespace Smansage\Dapodik\Laravel;

use Illuminate\Support\ServiceProvider;
use Smansage\Dapodik\DapodikClient;

class DapodikServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/dapodik.php' => config_path('dapodik.php'),
            ], 'dapodik-config');
        }
    }

    /**
     * Register package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/dapodik.php', 'dapodik');

        $this->app->singleton('dapodik', function ($app) {
            $config = $app['config']['dapodik'] ?? [];

            return new DapodikClient([
                'host' => $config['host'] ?? '127.0.0.1',
                'port' => $config['port'] ?? 5774,
                'baseUrl' => $config['base_url'] ?? null,
                'npsn' => (string) ($config['npsn'] ?? ''),
                'token' => (string) ($config['token'] ?? ''),
                'timeout' => (float) ($config['timeout'] ?? 30.0),
            ]);
        });

        $this->app->alias('dapodik', DapodikClient::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return ['dapodik', DapodikClient::class];
    }
}
