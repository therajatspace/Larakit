<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\SchemaManager;
use Therajatspace\Larakit\SEO\Schema\SchemaObject;
use Therajatspace\Larakit\SEO\Schema\SchemaContext;
use Therajatspace\Larakit\SEO\Schema\SchemaRelationshipResolver;

class SchemaManagerTest extends TestCase
{
    public function test_manager_can_create_schema_object(): void
    {
        $manager = $this->createSchemaManager();

        $schema = $manager->create();

        $this->assertInstanceOf(
            SchemaObject::class,
            $schema
        );
    }

    public function test_schema_object_contains_context(): void
    {
        $manager = $this->createSchemaManager();

        $schema = $manager->create();

        $data = $schema->toArray();

        $this->assertSame(
            'https://schema.org',
            $data['@context']
        );
    }

    public function test_schema_object_properties_are_stored(): void
    {
        $manager = $this->createSchemaManager();

        $schema = $manager->create();

        $data = $schema
            ->type('Article')
            ->name('LaraKit')
            ->description('A Laravel SEO package.')
            ->url('https://example.com')
            ->toArray();

        $this->assertSame(
            'Article',
            $data['@type']
        );

        $this->assertSame(
            'LaraKit',
            $data['name']
        );

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
        $manager = $this->createSchemaManager();

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
        $manager = $this->createSchemaManager();

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
        $manager = $this->createSchemaManager();

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
            1,
            substr_count(
                $result,
                '<script type="application/ld+json">'
            )
        );

        $this->assertStringContainsString(
            '"@graph"',
            $result
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
        $manager = $this->createSchemaManager();

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
        $manager = $this->createSchemaManager();

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

        $article = $data['@graph'][0];

        $this->assertSame(
            'Person',
            $article['author']['@type']
        );

        $this->assertSame(
            'Siddharth',
            $article['author']['name']
        );
    }

    public function test_manager_can_create_schema_using_class_name(): void
    {
        $manager = $this->createSchemaManager();

        $schema = $manager->create(
            \Therajatspace\Larakit\SEO\Schema\ProductSchema::class
        );

        $this->assertInstanceOf(
            \Therajatspace\Larakit\SEO\Schema\ProductSchema::class,
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

        $manager = $this->createSchemaManager();

        $manager->create(\stdClass::class);
    }

    public function test_factory_rejects_non_existing_class(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $manager = $this->createSchemaManager();

        $manager->create(
            'Therajatspace\Larakit\SEO\Schema\DoesNotExist'
        );
    }

    public function test_manager_can_generate_global_schema_id(): void
    {
        $manager = $this->createSchemaManager();

        $this->assertSame(
            'https://example.com/#organization',
            $manager->id('organization')
        );
    }

    public function test_manager_can_generate_current_page_schema_id(): void
    {
        $manager = $this->createSchemaManager();

        $this->assertSame(
            'https://example.com/test/#article',
            $manager->pageId('article')
        );
    }

    public function test_organization_gets_automatic_id(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization();

        $this->assertSame(
            'https://example.com/#organization',
            $organization->toArray()['@id']
        );
    }

    public function test_website_gets_automatic_id(): void
    {
        $manager = $this->createSchemaManager();

        $website = $manager->website();

        $this->assertSame(
            'https://example.com/#website',
            $website->toArray()['@id']
        );
    }

    public function test_article_gets_automatic_page_id(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article();

        $this->assertSame(
            'https://example.com/test/#article',
            $article->toArray()['@id']
        );
    }

    public function test_product_gets_automatic_page_id(): void
    {
        $manager = $this->createSchemaManager();

        $product = $manager->product();

        $this->assertSame(
            'https://example.com/test/#product',
            $product->toArray()['@id']
        );
    }

    public function test_breadcrumb_gets_automatic_page_id(): void
    {
        $manager = $this->createSchemaManager();

        $breadcrumb = $manager->breadcrumbs();

        $this->assertSame(
            'https://example.com/test/#breadcrumb',
            $breadcrumb->toArray()['@id']
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

    public function test_schemas_can_be_connected(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $manager->connect(
            $website,
            'publisher',
            $organization
        );

        $data = $website->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $data['publisher']
        );
    }

    public function test_website_is_automatically_connected_to_organization(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $manager->render();

        $this->assertSame(
            [
                '@id' => $organization->toArray()['@id'],
            ],
            $website->toArray()['publisher']
        );
    }

    public function test_article_is_automatically_connected_to_organization(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $article = $manager->article([
            'headline' => 'Laravel SEO',
        ]);

        $manager->render();

        $this->assertSame(
            [
                '@id' => $organization->toArray()['@id'],
            ],
            $article->toArray()['publisher']
        );
    }

    public function test_article_is_automatically_connected_to_website(): void
    {
        $manager = $this->createSchemaManager();

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $article = $manager->article([
            'headline' => 'Laravel SEO',
        ]);

        $manager->render();

        $this->assertSame(
            [
                '@id' => $website->toArray()['@id'],
            ],
            $article->toArray()['isPartOf']
        );
    }

    public function test_article_without_organization_has_no_publisher(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article([
            'headline' => 'Laravel SEO',
        ]);

        $manager->render();

        $this->assertArrayNotHasKey(
            'publisher',
            $article->toArray()
        );
    }

    public function test_article_without_website_has_no_is_part_of(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article([
            'headline' => 'Laravel SEO',
        ]);

        $manager->render();

        $this->assertArrayNotHasKey(
            'isPartOf',
            $article->toArray()
        );
    }

    public function test_schemas_without_ids_are_not_automatically_connected(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager
            ->create()
            ->type('Organization')
            ->name('LaraKit');

        $article = $manager
            ->create()
            ->type('Article')
            ->name('LaraKit Article');

        $manager->render();

        $this->assertArrayNotHasKey(
            'publisher',
            $article->toArray()
        );
    }
}