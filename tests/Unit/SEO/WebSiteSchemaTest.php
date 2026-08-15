<?php

namespace Sidd2604\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Sidd2604\Larakit\SEO\Schema\WebSiteSchema;
use Sidd2604\Larakit\SEO\Schema\SchemaManager;
use Sidd2604\Larakit\SEO\Schema\SchemaContext;
use Sidd2604\Larakit\SEO\Schema\SchemaRelationshipResolver;

class WebSiteSchemaTest extends TestCase
{
    public function test_website_has_correct_type(): void
    {
        $website = new WebSiteSchema();

        $this->assertSame(
            'WebSite',
            $website->toArray()['@type']
        );
    }

    public function test_website_uses_common_schema_properties(): void
    {
        $website = new WebSiteSchema();

        $data = $website
            ->name('LaraKit')
            ->description('Laravel SEO toolkit')
            ->url('https://example.com')
            ->toArray();

        $this->assertSame(
            'LaraKit',
            $data['name']
        );

        $this->assertSame(
            'Laravel SEO toolkit',
            $data['description']
        );

        $this->assertSame(
            'https://example.com',
            $data['url']
        );
    }

    public function test_website_can_be_created_from_data(): void
    {
        $manager = $this->createSchemaManager();

        $website = $manager->website([
            'name' => 'LaraKit',
            'url' => 'https://example.com',
            'description' => 'Laravel SEO toolkit',
        ]);

        $data = $website->toArray();

        $this->assertSame(
            'WebSite',
            $data['@type']
        );

        $this->assertSame(
            'LaraKit',
            $data['name']
        );

        $this->assertSame(
            'https://example.com',
            $data['url']
        );

        $this->assertSame(
            'Laravel SEO toolkit',
            $data['description']
        );
    }

    public function test_website_can_reference_publisher(): void
    {
        $website = new WebSiteSchema();

        $data = $website
            ->publisher('https://example.com/#organization')
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $data['publisher']
        );
    }

    protected function createSchemaManager(): SchemaManager
    {
        return new SchemaManager(
            new SchemaContext(
                'https://example.com',
                'https://example.com/test'
            ),
            new SchemaRelationshipResolver()
        );
    }
}