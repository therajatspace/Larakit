<?php

namespace Therajatspace\Larakit\Tests\Feature\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\OpenGraph\OpenGraphManager;
use Therajatspace\Larakit\SEO\Schema\FAQPageSchema;
use Therajatspace\Larakit\SEO\Schema\LocalBusinessSchema;
use Therajatspace\Larakit\SEO\Schema\SchemaConfigurator;
use Therajatspace\Larakit\SEO\Schema\SchemaContext;
use Therajatspace\Larakit\SEO\Schema\SchemaManager;
use Therajatspace\Larakit\SEO\Schema\SchemaRelationshipResolver;
use Therajatspace\Larakit\SEO\SeoManager;
use Therajatspace\Larakit\SEO\Twitter\TwitterCardManager;

class SeoSchemaRenderingTest extends TestCase
{
    protected function createSeoManager(): SeoManager
    {
        $schema = new SchemaManager(
            new SchemaContext(
                'https://example.com',
                'https://example.com/test'
            ),
            new SchemaRelationshipResolver()
        );

        $schemaConfigurator = new SchemaConfigurator(
            $schema
        );

        return new SeoManager(
            new OpenGraphManager(),
            new TwitterCardManager(),
            $schema,
            $schemaConfigurator
        );
    }

    public function test_article_factory_is_rendered_through_seo_manager(): void
    {
        $seo = $this->createSeoManager();

        $seo
            ->article()
            ->headline('Understanding Laravel');

        $html = $seo->render();

        $this->assertStringContainsString(
            '<script type="application/ld+json">',
            $html
        );

        $this->assertStringContainsString(
            '"@type":"Article"',
            $html
        );

        $this->assertStringContainsString(
            '"headline":"Understanding Laravel"',
            $html
        );
    }

    public function test_organization_factory_is_rendered_through_seo_manager(): void
    {
        $seo = $this->createSeoManager();

        $seo
            ->organization()
            ->name('LaraKit');

        $html = $seo->render();

        $this->assertStringContainsString(
            '"@type":"Organization"',
            $html
        );

        $this->assertStringContainsString(
            '"name":"LaraKit"',
            $html
        );
    }

    public function test_website_factory_is_rendered_through_seo_manager(): void
    {
        $seo = $this->createSeoManager();

        $seo
            ->website()
            ->name('LaraKit');

        $html = $seo->render();

        $this->assertStringContainsString(
            '"@type":"WebSite"',
            $html
        );

        $this->assertStringContainsString(
            '"name":"LaraKit"',
            $html
        );
    }

    public function test_product_factory_is_rendered_through_seo_manager(): void
    {
        $seo = $this->createSeoManager();

        $seo
            ->product()
            ->name('LaraKit Pro');

        $html = $seo->render();

        $this->assertStringContainsString(
            '"@type":"Product"',
            $html
        );

        $this->assertStringContainsString(
            '"name":"LaraKit Pro"',
            $html
        );
    }

    public function test_breadcrumb_factory_is_rendered_through_seo_manager(): void
    {
        $seo = $this->createSeoManager();

        $seo
            ->breadcrumbs()
            ->item(
                'Home',
                'https://example.com'
            )
            ->item(
                'Blog',
                'https://example.com/blog'
            );

        $html = $seo->render();

        $this->assertStringContainsString(
            '"@type":"BreadcrumbList"',
            $html
        );

        $this->assertStringContainsString(
            '"itemListElement"',
            $html
        );
    }

    public function test_person_factory_is_rendered_through_seo_manager(): void
    {
        $seo = $this->createSeoManager();

        $seo
            ->person()
            ->name('John Doe');

        $html = $seo->render();

        $this->assertStringContainsString(
            '"@type":"Person"',
            $html
        );

        $this->assertStringContainsString(
            '"name":"John Doe"',
            $html
        );
    }

    public function test_faq_page_factory_is_rendered_through_seo_manager(): void
    {
        $seo = $this->createSeoManager();

        $faq = $seo->faqPage();

        $faq->questions([
            [
                'question' => 'What is LaraKit?',
                'answer' => 'A Laravel SEO toolkit.',
            ],
        ]);

        $html = $seo->render();

        $this->assertStringContainsString(
            '"@type":"FAQPage"',
            $html
        );

        $this->assertStringContainsString(
            '"What is LaraKit?"',
            $html
        );

        $this->assertStringContainsString(
            '"A Laravel SEO toolkit."',
            $html
        );
    }

    public function test_web_page_factory_is_rendered_through_seo_manager(): void
    {
        $seo = $this->createSeoManager();

        $seo
            ->webPage()
            ->name('About LaraKit');

        $html = $seo->render();

        $this->assertStringContainsString(
            '"@type":"WebPage"',
            $html
        );

        $this->assertStringContainsString(
            '"name":"About LaraKit"',
            $html
        );
    }

    public function test_local_business_factory_is_rendered_through_seo_manager(): void
    {
        $seo = $this->createSeoManager();

        $seo
            ->localBusiness()
            ->name('LaraKit Office');

        $html = $seo->render();

        $this->assertStringContainsString(
            '"@type":"LocalBusiness"',
            $html
        );

        $this->assertStringContainsString(
            '"name":"LaraKit Office"',
            $html
        );
    }

    public function test_multiple_schema_types_are_rendered_in_same_json_ld_graph(): void
    {
        $seo = $this->createSeoManager();

        $seo
            ->organization()
            ->name('LaraKit');

        $seo
            ->person()
            ->name('John Doe');

        $html = $seo->render();

        $this->assertSame(
            1,
            substr_count(
                $html,
                '<script type="application/ld+json">'
            )
        );

        $this->assertStringContainsString(
            '"@type":"Organization"',
            $html
        );

        $this->assertStringContainsString(
            '"@type":"Person"',
            $html
        );
    }

    public function test_schema_output_is_a_single_json_ld_graph(): void
    {
        $seo = $this->createSeoManager();

        $seo
            ->person()
            ->name('John Doe');

        $html = $seo->render();

        $this->assertSame(
            1,
            substr_count(
                $html,
                '<script type="application/ld+json">'
            )
        );

        $this->assertSame(
            1,
            substr_count(
                $html,
                '</script>'
            )
        );

        $this->assertStringContainsString(
            '"@context":"https://schema.org"',
            $html
        );

        $this->assertStringContainsString(
            '"@graph"',
            $html
        );
    }

    public function test_schema_is_rendered_alongside_other_seo_output(): void
    {
        $seo = $this->createSeoManager();

        $seo
            ->title('LaraKit')
            ->description('Laravel SEO toolkit')
            ->canonical('https://example.com');

        $seo
            ->person()
            ->name('John Doe');

        $html = $seo->render();

        $this->assertStringContainsString(
            '<title>LaraKit</title>',
            $html
        );

        $this->assertStringContainsString(
            '<meta name="description"',
            $html
        );

        $this->assertStringContainsString(
            '<link rel="canonical"',
            $html
        );

        $this->assertStringContainsString(
            '<script type="application/ld+json">',
            $html
        );

        $this->assertStringContainsString(
            '"@type":"Person"',
            $html
        );
    }
}