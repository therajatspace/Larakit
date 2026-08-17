<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\SchemaConfigurator;
use Therajatspace\Larakit\SEO\Schema\SchemaManager;
use Therajatspace\Larakit\SEO\Schema\SchemaContext;
use Therajatspace\Larakit\SEO\Schema\SchemaRelationshipResolver;

class SchemaConfiguratorTest extends TestCase
{
    protected SchemaManager $schema;

    protected SchemaConfigurator $configurator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schema = $this->createSchemaManager();

        $this->configurator = new SchemaConfigurator(
            $this->schema
        );
    }

    public function test_organization_and_website_are_configured(): void
    {
        $this->configurator->configure([
            'schema' => [
                'auto' => true,
            ],

            'organization' => [
                'name' => 'LaraKit',
                'url' => 'https://example.com',
                'logo' => 'https://example.com/logo.png',
                'same_as' => [
                    'https://github.com/example',
                ],
            ],

            'website' => [
                'name' => 'LaraKit',
                'url' => 'https://example.com',
                'description' => 'Laravel SEO toolkit',
            ],
        ]);

        $this->assertSame(
            2,
            $this->schema->count()
        );
    }

    public function test_auto_configuration_can_be_disabled(): void
    {
        $this->configurator->configure([
            'schema' => [
                'auto' => false,
            ],

            'organization' => [
                'name' => 'LaraKit',
            ],

            'website' => [
                'name' => 'LaraKit',
            ],
        ]);

        $this->assertSame(
            0,
            $this->schema->count()
        );
    }

    public function test_missing_organization_does_not_create_schema(): void
    {
        $this->configurator->configure([
            'schema' => [
                'auto' => true,
            ],

            'website' => [
                'name' => 'LaraKit',
            ],
        ]);

        $this->assertSame(
            1,
            $this->schema->count()
        );
    }

    public function test_missing_website_does_not_create_schema(): void
    {
        $this->configurator->configure([
            'schema' => [
                'auto' => true,
            ],

            'organization' => [
                'name' => 'LaraKit',
            ],
        ]);

        $this->assertSame(
            1,
            $this->schema->count()
        );
    }

    public function test_empty_configuration_creates_no_schema(): void
    {
        $this->configurator->configure([]);

        $this->assertSame(
            0,
            $this->schema->count()
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