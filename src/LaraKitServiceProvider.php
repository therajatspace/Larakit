<?php

namespace Sidd2604\Larakit;

use Illuminate\Support\ServiceProvider;
use Sidd2604\Larakit\Console\LaraKitWelcome;
use Sidd2604\Larakit\SEO\SeoManager;
use Illuminate\Support\Facades\Blade;
use Sidd2604\Larakit\SEO\OpenGraph\OpenGraphManager;


class LaraKitServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/larakit.php',
            'larakit'
        );

        $this->app->singleton(SeoManager::class, function ($app) {
            return new SeoManager(
                $app->make(OpenGraphManager::class),
                config('larakit.seo.defaults', [])
            );
        });

        $this->app->singleton(OpenGraphManager::class);
    }

    public function boot()
    {
        if (
            $this->app->runningInConsole() &&
            in_array('package:discover', $_SERVER['argv'] ?? [])
        ) {
            LaraKitWelcome::show();
        }

        Blade::directive('seo', function () {
            return "<?php echo app(\Sidd2604\Larakit\SEO\SeoManager::class)->render(); ?>";
        });
    }
}
