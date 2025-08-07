<?php

namespace MostafaAminFlakes\DualOptimus;

use Illuminate\Support\ServiceProvider;
use MostafaAminFlakes\DualOptimus\Console\Commands\GenerateOptimusKeysCommand;

class DualOptimusServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->singleton('dual-optimus', function ($app) {
            return new DualOptimusManager($app);
        });

        $this->app->alias('dual-optimus', DualOptimusManager::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateOptimusKeysCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/dual-optimus.php' => config_path('dual-optimus.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../config/dual-optimus.php', 'dual-optimus');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['dual-optimus', DualOptimusManager::class];
    }
}
