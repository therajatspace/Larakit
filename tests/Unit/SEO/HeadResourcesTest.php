<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\OpenGraph\OpenGraphManager;
use Therajatspace\Larakit\SEO\Schema\SchemaConfigurator;
use Therajatspace\Larakit\SEO\Schema\SchemaContext;
use Therajatspace\Larakit\SEO\Schema\SchemaManager;
use Therajatspace\Larakit\SEO\Schema\SchemaRelationshipResolver;
use Therajatspace\Larakit\SEO\SeoManager;
use Therajatspace\Larakit\SEO\Twitter\TwitterCardManager;

class HeadResourcesTest extends TestCase
{
    protected function makeSeo(array $head = []): SeoManager
    {
        $schemaContext = new SchemaContext(
            'http://localhost',
            'http://localhost/test'
        );

        $schemaManager = new SchemaManager(
            $schemaContext,
            new SchemaRelationshipResolver()
        );

        $schemaConfigurator = new SchemaConfigurator(
            $schemaManager
        );

        return new SeoManager(
            new OpenGraphManager(),
            new TwitterCardManager(),
            $schemaManager,
            $schemaConfigurator,
            [],
            $head
        );
    }

    public function test_favicon_is_not_rendered_when_disabled(): void
    {
        $seo = $this->makeSeo([
            'favicon' => [
                'enabled' => false,
                'url' => '/favicon.ico',
            ],
        ]);

        $html = $seo->render();

        $this->assertStringNotContainsString(
            '<link rel="icon"',
            $html
        );
    }

    public function test_favicon_is_rendered_from_global_configuration(): void
    {
        $seo = $this->makeSeo([
            'favicon' => [
                'enabled' => true,
                'url' => '/favicon.ico',
            ],
        ]);

        $html = $seo->render();

        $this->assertStringContainsString(
            '<link rel="icon" href="/favicon.ico">',
            $html
        );
    }

    public function test_favicon_page_override_takes_precedence_over_global_configuration(): void
    {
        $seo = $this->makeSeo([
            'favicon' => [
                'enabled' => true,
                'url' => '/favicon.ico',
            ],
        ]);

        $seo->favicon('/special-favicon.ico');

        $html = $seo->render();

        $this->assertStringContainsString(
            '<link rel="icon" href="/special-favicon.ico">',
            $html
        );

        $this->assertStringNotContainsString(
            '<link rel="icon" href="/favicon.ico">',
            $html
        );
    }

    public function test_favicon_can_be_disabled_for_a_specific_page(): void
    {
        $seo = $this->makeSeo([
            'favicon' => [
                'enabled' => true,
                'url' => '/favicon.ico',
            ],
        ]);

        $seo->withoutFavicon();

        $html = $seo->render();

        $this->assertStringNotContainsString(
            '<link rel="icon"',
            $html
        );
    }

    public function test_favicon_can_define_a_mime_type(): void
    {
        $seo = $this->makeSeo();

        $seo->favicon(
            '/favicon.png',
            'image/png'
        );

        $html = $seo->render();

        $this->assertStringContainsString(
            '<link rel="icon" href="/favicon.png" type="image/png">',
            $html
        );
    }

    public function test_apple_touch_icon_is_not_rendered_when_disabled(): void
    {
        $seo = $this->makeSeo([
            'apple_touch_icon' => [
                'enabled' => false,
                'url' => '/apple-touch-icon.png',
            ],
        ]);

        $html = $seo->render();

        $this->assertStringNotContainsString(
            '<link rel="apple-touch-icon"',
            $html
        );
    }

    public function test_apple_touch_icon_is_rendered_from_global_configuration(): void
    {
        $seo = $this->makeSeo([
            'apple_touch_icon' => [
                'enabled' => true,
                'url' => '/apple-touch-icon.png',
            ],
        ]);

        $html = $seo->render();

        $this->assertStringContainsString(
            '<link rel="apple-touch-icon" href="/apple-touch-icon.png">',
            $html
        );
    }

    public function test_apple_touch_icon_page_override_takes_precedence(): void
    {
        $seo = $this->makeSeo([
            'apple_touch_icon' => [
                'enabled' => true,
                'url' => '/apple-touch-icon.png',
            ],
        ]);

        $seo->appleTouchIcon('/special-apple-icon.png');

        $html = $seo->render();

        $this->assertStringContainsString(
            '<link rel="apple-touch-icon" href="/special-apple-icon.png">',
            $html
        );

        $this->assertStringNotContainsString(
            '<link rel="apple-touch-icon" href="/apple-touch-icon.png">',
            $html
        );
    }

    public function test_apple_touch_icon_can_be_disabled_for_a_specific_page(): void
    {
        $seo = $this->makeSeo([
            'apple_touch_icon' => [
                'enabled' => true,
                'url' => '/apple-touch-icon.png',
            ],
        ]);

        $seo->withoutAppleTouchIcon();

        $html = $seo->render();

        $this->assertStringNotContainsString(
            '<link rel="apple-touch-icon"',
            $html
        );
    }

    public function test_manifest_is_not_rendered_when_disabled(): void
    {
        $seo = $this->makeSeo([
            'manifest' => [
                'enabled' => false,
                'url' => '/manifest.json',
            ],
        ]);

        $html = $seo->render();

        $this->assertStringNotContainsString(
            '<link rel="manifest"',
            $html
        );
    }

    public function test_manifest_is_rendered_from_global_configuration(): void
    {
        $seo = $this->makeSeo([
            'manifest' => [
                'enabled' => true,
                'url' => '/manifest.json',
            ],
        ]);

        $html = $seo->render();

        $this->assertStringContainsString(
            '<link rel="manifest" href="/manifest.json">',
            $html
        );
    }

    public function test_manifest_page_override_takes_precedence(): void
    {
        $seo = $this->makeSeo([
            'manifest' => [
                'enabled' => true,
                'url' => '/manifest.json',
            ],
        ]);

        $seo->manifest('/special-manifest.json');

        $html = $seo->render();

        $this->assertStringContainsString(
            '<link rel="manifest" href="/special-manifest.json">',
            $html
        );

        $this->assertStringNotContainsString(
            '<link rel="manifest" href="/manifest.json">',
            $html
        );
    }

    public function test_manifest_can_be_disabled_for_a_specific_page(): void
    {
        $seo = $this->makeSeo([
            'manifest' => [
                'enabled' => true,
                'url' => '/manifest.json',
            ],
        ]);

        $seo->withoutManifest();

        $html = $seo->render();

        $this->assertStringNotContainsString(
            '<link rel="manifest"',
            $html
        );
    }

    public function test_head_resource_urls_are_html_escaped(): void
    {
        $seo = $this->makeSeo();

        $seo
            ->favicon('/favicon?x="test"')
            ->appleTouchIcon('/apple?x="test"')
            ->manifest('/manifest?x="test"');

        $html = $seo->render();

        $this->assertStringContainsString(
            '/favicon?x=&quot;test&quot;',
            $html
        );

        $this->assertStringContainsString(
            '/apple?x=&quot;test&quot;',
            $html
        );

        $this->assertStringContainsString(
            '/manifest?x=&quot;test&quot;',
            $html
        );
    }
}