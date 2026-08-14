<?php

namespace Sidd2604\Larakit;

use Illuminate\Support\ServiceProvider;
use Sidd2604\Larakit\Console\LaraKitWelcome;
use Sidd2604\Larakit\SEO\SeoManager;


class LaraKitServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SeoManager::class, function () {
                return new SeoManager();
            }
        );
    }

    public function boot()
    {
        if (
            $this->app->runningInConsole() &&
            in_array('package:discover', $_SERVER['argv'] ?? [])
        ) {
            LaraKitWelcome::show();
        }
    }
}
