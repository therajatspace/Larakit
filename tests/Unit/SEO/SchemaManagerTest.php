<?php

namespace Sidd2604\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Sidd2604\Larakit\SEO\Schema\SchemaManager;
use Sidd2604\Larakit\SEO\Schema\SchemaObject;

class SchemaManagerTest extends TestCase
{
    public function test_manager_can_create_schema_object(): void
    {
        $manager = new SchemaManager();

        $schema = $manager->create();

        $this->assertInstanceOf(
            SchemaObject::class,
            $schema
        );
    }

    public function test_schema_object_contains_context(): void
    {
        $manager = new SchemaManager();

        $schema = $manager->create();

        $data = $schema->toArray();

        $this->assertSame(
            'https://schema.org',
            $data['@context']
        );
    }

    public function test_schema_object_properties_are_stored(): void
    {
        $manager = new SchemaManager();

        $schema = $manager->create();

        $data = $schema
            ->type('Article')
            ->name('LaraKit')
            ->description('A Laravel SEO package.')
            ->url('https://example.com')
            ->toArray();

        $this->assertSame('Article', $data['@type']);
        $this->assertSame('LaraKit', $data['name']);
        $this->assertSame(
            'A Laravel SEO package.',
            $data['description']
        );
        $this->assertSame(
            'https://example.com',
            $data['url']
        );
    }

    public function test_custom_property_is_stored(): void
    {
        $manager = new SchemaManager();

        $schema = $manager->create();

        $data = $schema
            ->type('Article')
            ->property('author', [
                '@type' => 'Person',
                'name' => 'Siddharth',
            ])
            ->toArray();

        $this->assertSame(
            [
                '@type' => 'Person',
                'name' => 'Siddharth',
            ],
            $data['author']
        );
    }

    public function test_schema_is_rendered_as_json_ld(): void
    {
        $manager = new SchemaManager();

        $manager
            ->create()
            ->type('Article')
            ->name('LaraKit');

        $result = $manager->render();

        $this->assertStringContainsString(
            '<script type="application/ld+json">',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"Article"',
            $result
        );

        $this->assertStringContainsString(
            '"name":"LaraKit"',
            $result
        );
    }

    public function test_multiple_schema_objects_are_rendered(): void
    {
        $manager = new SchemaManager();

        $manager
            ->create()
            ->type('Article')
            ->name('LaraKit Article');

        $manager
            ->create()
            ->type('Organization')
            ->name('LaraKit');

        $result = $manager->render();

        $this->assertSame(
            2,
            substr_count(
                $result,
                '<script type="application/ld+json">'
            )
        );

        $this->assertStringContainsString(
            '"@type":"Article"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"Organization"',
            $result
        );
    }

    public function test_schema_objects_are_independent(): void
    {
        $manager = new SchemaManager();

        $article = $manager
            ->create()
            ->type('Article')
            ->name('My Article');

        $organization = $manager
            ->create()
            ->type('Organization')
            ->name('LaraKit');

        $articleData = $article->toArray();
        $organizationData = $organization->toArray();

        $this->assertSame(
            'Article',
            $articleData['@type']
        );

        $this->assertSame(
            'Organization',
            $organizationData['@type']
        );

        $this->assertNotSame(
            $articleData['@type'],
            $organizationData['@type']
        );
    }
    public function test_nested_schema_property_is_valid_json(): void
    {
        $manager = new SchemaManager();

        $manager
            ->create()
            ->type('Article')
            ->property('author', [
                '@type' => 'Person',
                'name' => 'Siddharth',
            ]);

        $result = $manager->render();

        preg_match(
            '/<script type="application\/ld\+json">(.*?)<\/script>/s',
            $result,
            $matches
        );

        $this->assertNotEmpty($matches);

        $data = json_decode(
            $matches[1],
            true
        );

        $this->assertSame(
            JSON_ERROR_NONE,
            json_last_error()
        );

        $this->assertSame(
            'Person',
            $data['author']['@type']
        );

        $this->assertSame(
            'Siddharth',
            $data['author']['name']
        );
    }
    public function test_manager_can_create_schema_using_class_name(): void
    {
        $manager = new SchemaManager();

        $schema = $manager->create(
            \Sidd2604\Larakit\SEO\Schema\ProductSchema::class
        );

        $this->assertInstanceOf(
            \Sidd2604\Larakit\SEO\Schema\ProductSchema::class,
            $schema
        );

        $this->assertSame(
            1,
            $manager->count()
        );
    }
    public function test_factory_rejects_non_schema_class(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $manager = new SchemaManager();

        $manager->create(\stdClass::class);
    }
    public function test_factory_rejects_non_existing_class(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $manager = new SchemaManager();

        $manager->create(
            'Sidd2604\Larakit\SEO\Schema\DoesNotExist'
        );
    }
}