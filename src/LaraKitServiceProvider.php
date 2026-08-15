<?php

namespace Sidd2604\Larakit;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Sidd2604\Larakit\Console\LaraKitWelcome;
use Sidd2604\Larakit\SEO\OpenGraph\OpenGraphManager;
use Sidd2604\Larakit\SEO\Schema\SchemaConfigurator;
use Sidd2604\Larakit\SEO\Schema\SchemaContext;
use Sidd2604\Larakit\SEO\Schema\SchemaManager;
use Sidd2604\Larakit\SEO\Schema\SchemaRelationshipResolver;
use Sidd2604\Larakit\SEO\SeoManager;
use Sidd2604\Larakit\SEO\Twitter\TwitterCardManager;

class LaraKitServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/larakit.php',
            'larakit'
        );

        /*
        |--------------------------------------------------------------------------
        | Schema Context
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            SchemaContext::class,
            function ($app) {
                return new SchemaContext(
                    config('app.url'),
                    $app['request']->url()
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Schema Relationship Resolver
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            SchemaRelationshipResolver::class
        );

        /*
        |--------------------------------------------------------------------------
        | Schema Manager
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            SchemaManager::class,
            function ($app) {
                return new SchemaManager(
                    $app->make(SchemaContext::class),
                    $app->make(SchemaRelationshipResolver::class)
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | SEO Managers
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            OpenGraphManager::class
        );

        $this->app->singleton(
            TwitterCardManager::class
        );

        $this->app->singleton(
            SchemaConfigurator::class
        );

        $this->app->singleton(
            SeoManager::class,
            function ($app) {
                return new SeoManager(
                    $app->make(OpenGraphManager::class),
                    $app->make(TwitterCardManager::class),
                    $app->make(SchemaManager::class),
                    config('larakit.seo.defaults', [])
                );
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

        Blade::directive('seo', function () {
            return "<?php echo app(\Sidd2604\Larakit\SEO\SeoManager::class)->render(); ?>";
        });
    }
}