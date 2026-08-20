<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\BreadcrumbSchema;
use Therajatspace\Larakit\SEO\Schema\SchemaContext;
use Therajatspace\Larakit\SEO\Schema\SchemaManager;
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

    public function test_items_are_added_with_positions(): void
    {
        $breadcrumb = new BreadcrumbSchema();

        $data = $breadcrumb
            ->item('Home', 'https://example.com')
            ->item('Blog', 'https://example.com/blog')
            ->item(
                'Laravel',
                'https://example.com/blog/laravel'
            )
            ->toArray();

        $this->assertCount(
            3,
            $data['itemListElement']
        );

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
            ->item(
                'Home',
                'https://example.com'
            )
            ->item(
                'Blog',
                'https://example.com/blog'
            );

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

    public function test_breadcrumb_can_have_id(): void
    {
        $breadcrumb = new BreadcrumbSchema();

        $data = $breadcrumb
            ->id('https://example.com/#breadcrumb')
            ->item(
                'Home',
                'https://example.com'
            )
            ->toArray();

        $this->assertSame(
            'https://example.com/#breadcrumb',
            $data['@id']
        );
    }

    public function test_from_array_accepts_id(): void
    {
        $breadcrumb = new BreadcrumbSchema();

        $data = $breadcrumb
            ->fromArray([
                'id' => 'https://example.com/#breadcrumb',
                'items' => [
                    [
                        'name' => 'Home',
                        'url' => 'https://example.com',
                    ],
                ],
            ])
            ->toArray();

        $this->assertSame(
            'https://example.com/#breadcrumb',
            $data['@id']
        );

        $this->assertCount(
            1,
            $data['itemListElement']
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

    public function test_items_are_fluent(): void
    {
        $breadcrumb = new BreadcrumbSchema();

        $this->assertSame(
            $breadcrumb,
            $breadcrumb->item(
                'Home',
                'https://example.com'
            )
        );
    }

    public function test_id_and_items_can_be_combined(): void
    {
        $breadcrumb = new BreadcrumbSchema();

        $data = $breadcrumb
            ->id('https://example.com/#breadcrumb')
            ->item(
                'Home',
                'https://example.com'
            )
            ->item(
                'Blog',
                'https://example.com/blog'
            )
            ->toArray();

        $this->assertSame(
            'https://example.com/#breadcrumb',
            $data['@id']
        );

        $this->assertCount(
            2,
            $data['itemListElement']
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