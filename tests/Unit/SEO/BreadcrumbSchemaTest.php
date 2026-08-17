<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\BreadcrumbSchema;
use Therajatspace\Larakit\SEO\Schema\SchemaManager;
use Therajatspace\Larakit\SEO\Schema\SchemaContext;
use Therajatspace\Larakit\SEO\Schema\SchemaRelationshipResolver;

class BreadcrumbSchemaTest extends TestCase
{
    public function test_breadcrumb_has_correct_type(): void
    {
        $breadcrumb = new BreadcrumbSchema();

        $this->assertSame(
            'BreadcrumbList',
            $breadcrumb->toArray()['@type']
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

    public function test_items_are_added_with_positions(): void
    {
        $breadcrumb = new BreadcrumbSchema();

        $data = $breadcrumb
            ->item('Home', 'https://example.com')
            ->item('Blog', 'https://example.com/blog')
            ->item('Laravel', 'https://example.com/blog/laravel')
            ->toArray();

        $this->assertCount(3, $data['itemListElement']);

        $this->assertSame(
            1,
            $data['itemListElement'][0]['position']
        );

        $this->assertSame(
            'Home',
            $data['itemListElement'][0]['name']
        );

        $this->assertSame(
            2,
            $data['itemListElement'][1]['position']
        );

        $this->assertSame(
            'Blog',
            $data['itemListElement'][1]['name']
        );

        $this->assertSame(
            3,
            $data['itemListElement'][2]['position']
        );

        $this->assertSame(
            'Laravel',
            $data['itemListElement'][2]['name']
        );
    }

    public function test_breadcrumb_items_are_rendered_as_json_ld(): void
    {
        $manager = $this->createSchemaManager();

        $manager
            ->breadcrumbs()
            ->item('Home', 'https://example.com')
            ->item('Blog', 'https://example.com/blog');

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"BreadcrumbList"',
            $result
        );

        $this->assertStringContainsString(
            '"itemListElement"',
            $result
        );

        $this->assertStringContainsString(
            '"position":1',
            $result
        );

        $this->assertStringContainsString(
            '"position":2',
            $result
        );
    }

    public function test_from_array_validates_breadcrumb_urls(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $breadcrumb = new BreadcrumbSchema();

        $breadcrumb->fromArray([
            'items' => [
                [
                    'name' => 'Home',
                    'url' => 'banana',
                ],
            ],
        ]);
    }
}