<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\SchemaObject;

class SchemaObjectTest extends TestCase
{
    public function test_schema_has_schema_org_context(): void
    {
        $schema = new SchemaObject();

        $data = $schema->toArray();

        $this->assertSame(
            'https://schema.org',
            $data['@context']
        );
    }

    public function test_schema_can_set_type(): void
    {
        $schema = new SchemaObject();

        $data = $schema
            ->type('Article')
            ->toArray();

        $this->assertSame(
            'Article',
            $data['@type']
        );
    }

    public function test_schema_can_set_id(): void
    {
        $schema = new SchemaObject();

        $data = $schema
            ->id('https://example.com/#article')
            ->toArray();

        $this->assertSame(
            'https://example.com/#article',
            $data['@id']
        );
    }

    public function test_schema_can_set_name(): void
    {
        $schema = new SchemaObject();

        $data = $schema
            ->name('LaraKit')
            ->toArray();

        $this->assertSame(
            'LaraKit',
            $data['name']
        );
    }

    public function test_schema_can_set_description(): void
    {
        $schema = new SchemaObject();

        $data = $schema
            ->description('Laravel SEO toolkit')
            ->toArray();

        $this->assertSame(
            'Laravel SEO toolkit',
            $data['description']
        );
    }

    public function test_schema_can_set_url(): void
    {
        $schema = new SchemaObject();

        $data = $schema
            ->url('https://example.com')
            ->toArray();

        $this->assertSame(
            'https://example.com',
            $data['url']
        );
    }

    public function test_schema_can_set_arbitrary_property(): void
    {
        $schema = new SchemaObject();

        $data = $schema
            ->property('headline', 'My Article')
            ->toArray();

        $this->assertSame(
            'My Article',
            $data['headline']
        );
    }

    public function test_schema_can_be_created_from_array(): void
    {
        $schema = new SchemaObject();

        $data = $schema
            ->fromArray([
                'name' => 'LaraKit',
                'description' => 'Laravel SEO toolkit',
                'headline' => 'Learning SEO',
            ])
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
            'Learning SEO',
            $data['headline']
        );
    }

    public function test_methods_are_fluent(): void
    {
        $schema = new SchemaObject();

        $result = $schema
            ->id('https://example.com/#article')
            ->type('Article')
            ->name('LaraKit');

        $this->assertSame(
            $schema,
            $result
        );
    }
    public function test_schema_can_have_an_id(): void
{
    $schema = new SchemaObject();

    $data = $schema
        ->id('https://example.com/#organization')
        ->toArray();

    $this->assertSame(
        'https://example.com/#organization',
        $data['@id']
    );
}
}