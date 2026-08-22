<?php

namespace Therajatspace\Larakit;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Therajatspace\Larakit\Console\LaraKitWelcome;
use Therajatspace\Larakit\SEO\OpenGraph\OpenGraphManager;
use Therajatspace\Larakit\SEO\Schema\SchemaConfigurator;
use Therajatspace\Larakit\SEO\Schema\SchemaContext;
use Therajatspace\Larakit\SEO\Schema\SchemaManager;
use Therajatspace\Larakit\SEO\Schema\SchemaRelationshipResolver;
use Therajatspace\Larakit\SEO\SeoManager;
use Therajatspace\Larakit\SEO\Twitter\TwitterCardManager;
use Therajatspace\Larakit\Console\Commands\LaraKitInstall;

use Therajatspace\Larakit\Auth\AuthServiceProvider;
use Therajatspace\Larakit\Auth\AuthRouteRegistrar;

class LaraKitServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/larakit.php',
            'larakit'
        );

        $this->app->register(AuthServiceProvider::class);

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
                    $app->make(SchemaConfigurator::class),
                    config('larakit.seo.defaults', []),
                    config('larakit.seo.head', [])
                );
            }
        );


    }

    public function boot(): void
    {
        if (
            $this->app->runningInConsole() &&
            in_array('package:discover', $_SERVER['argv'] ?? [])
        ) {
            LaraKitWelcome::show();
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                LaraKitInstall::class,
            ]);
        }

        Blade::directive('seo', function () {
            return "<?php echo app(\Therajatspace\Larakit\SEO\SeoManager::class)->render(); ?>";
        });

        app(AuthRouteRegistrar::class)->register();
    }
}
