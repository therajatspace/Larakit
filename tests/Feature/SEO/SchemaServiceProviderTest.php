<?php

namespace Therajatspace\Larakit\Tests\Feature\SEO;

use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\LaraKitServiceProvider;
use Therajatspace\Larakit\SEO\SeoManager;
use Therajatspace\Larakit\SEO\Schema\SchemaContext;
use Therajatspace\Larakit\SEO\Schema\SchemaManager;
use Therajatspace\Larakit\SEO\Schema\SchemaRelationshipResolver;

class SchemaServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
        ];
    }

    public function test_schema_manager_can_be_resolved_from_container(): void
    {
        $manager = $this->app->make(
            SchemaManager::class
        );

        $this->assertInstanceOf(
            SchemaManager::class,
            $manager
        );
    }

    public function test_schema_context_can_be_resolved_from_container(): void
    {
        $context = $this->app->make(
            SchemaContext::class
        );

        $this->assertInstanceOf(
            SchemaContext::class,
            $context
        );
    }

    public function test_relationship_resolver_can_be_resolved_from_container(): void
    {
        $resolver = $this->app->make(
            SchemaRelationshipResolver::class
        );

        $this->assertInstanceOf(
            SchemaRelationshipResolver::class,
            $resolver
        );
    }

    public function test_seo_manager_can_be_resolved_from_container(): void
    {
        $manager = $this->app->make(
            SeoManager::class
        );

        $this->assertInstanceOf(
            SeoManager::class,
            $manager
        );
    }

    public function test_schema_manager_uses_registered_dependencies(): void
    {
        $manager = $this->app->make(
            SchemaManager::class
        );

        $schema = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $this->assertSame(
            'Organization',
            $schema->toArray()['@type']
        );

        $this->assertSame(
            'http://localhost/#organization',
            $schema->toArray()['@id']
        );
    }

    public function test_person_schema_is_available_from_schema_manager(): void
    {
        $manager = app(
            \Therajatspace\Larakit\SEO\Schema\SchemaManager::class
        );

        $person = $manager->person([
            'name' => 'Siddharth Sharma',
            'url' => 'https://example.com/about',
        ]);

        $this->assertSame(
            'Person',
            $person->toArray()['@type']
        );

        $this->assertSame(
            'Siddharth Sharma',
            $person->toArray()['name']
        );
    }
}