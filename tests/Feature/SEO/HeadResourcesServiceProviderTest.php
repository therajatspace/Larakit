<?php

namespace Therajatspace\Larakit\Tests\Feature\SEO;

use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\LaraKitServiceProvider;
use Therajatspace\Larakit\SEO\SeoManager;

class HeadResourcesServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
        ];
    }

    public function test_seo_manager_receives_head_configuration(): void
    {
        config()->set('larakit.seo.head', [
            'favicon' => [
                'enabled' => true,
                'url' => '/favicon.ico',
                'type' => null,
            ],

            'apple_touch_icon' => [
                'enabled' => true,
                'url' => '/apple-touch-icon.png',
            ],

            'manifest' => [
                'enabled' => true,
                'url' => '/manifest.json',
            ],
        ]);

        $seo = $this->app->make(SeoManager::class);

        $html = $seo->render();

        $this->assertStringContainsString(
            '<link rel="icon" href="/favicon.ico">',
            $html
        );

        $this->assertStringContainsString(
            '<link rel="apple-touch-icon" href="/apple-touch-icon.png">',
            $html
        );

        $this->assertStringContainsString(
            '<link rel="manifest" href="/manifest.json">',
            $html
        );
    }

    public function test_head_resources_are_not_rendered_when_disabled(): void
    {
        config()->set('larakit.seo.head', [
            'favicon' => [
                'enabled' => false,
                'url' => '/favicon.ico',
            ],

            'apple_touch_icon' => [
                'enabled' => false,
                'url' => '/apple-touch-icon.png',
            ],

            'manifest' => [
                'enabled' => false,
                'url' => '/manifest.json',
            ],
        ]);

        $seo = $this->app->make(SeoManager::class);

        $html = $seo->render();

        $this->assertStringNotContainsString(
            '<link rel="icon"',
            $html
        );

        $this->assertStringNotContainsString(
            '<link rel="apple-touch-icon"',
            $html
        );

        $this->assertStringNotContainsString(
            '<link rel="manifest"',
            $html
        );
    }

    public function test_page_level_values_override_service_provider_configuration(): void
    {
        config()->set('larakit.seo.head', [
            'favicon' => [
                'enabled' => true,
                'url' => '/global-favicon.ico',
            ],

            'apple_touch_icon' => [
                'enabled' => true,
                'url' => '/global-apple-icon.png',
            ],

            'manifest' => [
                'enabled' => true,
                'url' => '/global-manifest.json',
            ],
        ]);

        $seo = $this->app->make(SeoManager::class);

        $seo
            ->favicon('/page-favicon.ico')
            ->appleTouchIcon('/page-apple-icon.png')
            ->manifest('/page-manifest.json');

        $html = $seo->render();

        $this->assertStringContainsString(
            '<link rel="icon" href="/page-favicon.ico">',
            $html
        );

        $this->assertStringContainsString(
            '<link rel="apple-touch-icon" href="/page-apple-icon.png">',
            $html
        );

        $this->assertStringContainsString(
            '<link rel="manifest" href="/page-manifest.json">',
            $html
        );

        $this->assertStringNotContainsString(
            '<link rel="icon" href="/global-favicon.ico">',
            $html
        );

        $this->assertStringNotContainsString(
            '<link rel="apple-touch-icon" href="/global-apple-icon.png">',
            $html
        );

        $this->assertStringNotContainsString(
            '<link rel="manifest" href="/global-manifest.json">',
            $html
        );
    }

    public function test_page_level_disable_overrides_enabled_service_provider_configuration(): void
    {
        config()->set('larakit.seo.head', [
            'favicon' => [
                'enabled' => true,
                'url' => '/global-favicon.ico',
            ],

            'apple_touch_icon' => [
                'enabled' => true,
                'url' => '/global-apple-icon.png',
            ],

            'manifest' => [
                'enabled' => true,
                'url' => '/global-manifest.json',
            ],
        ]);

        $seo = $this->app->make(SeoManager::class);

        $seo
            ->withoutFavicon()
            ->withoutAppleTouchIcon()
            ->withoutManifest();

        $html = $seo->render();

        $this->assertStringNotContainsString(
            '<link rel="icon"',
            $html
        );

        $this->assertStringNotContainsString(
            '<link rel="apple-touch-icon"',
            $html
        );

        $this->assertStringNotContainsString(
            '<link rel="manifest"',
            $html
        );
    }
}