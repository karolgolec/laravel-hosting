<?php

namespace KarolGolec\LaravelHosting;

use Illuminate\Console\Scheduling\Schedule;
use KarolGolec\LaravelHosting\Console\Commands\Install;
use KarolGolec\LaravelHosting\Console\Commands\RunQueueWork;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/laravel-hosting.php' => config_path('laravel-hosting.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Install::class,
                RunQueueWork::class,
            ]);
        }

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            if (config('laravel-hosting.queue_enabled')) {
                $schedule->command('queue:work:hosting')->everyMinute();
            }
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-hosting.php', 'laravel-hosting'
        );
    }
}
