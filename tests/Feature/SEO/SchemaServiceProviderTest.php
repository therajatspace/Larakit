<?php

namespace Sidd2604\Larakit\Tests\Feature\SEO;

use Orchestra\Testbench\TestCase;
use Sidd2604\Larakit\LaraKitServiceProvider;
use Sidd2604\Larakit\SEO\Schema\SchemaContext;
use Sidd2604\Larakit\SEO\Schema\SchemaManager;
use Sidd2604\Larakit\SEO\Schema\SchemaRelationshipResolver;

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
}