<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\OrganizationSchema;
use Therajatspace\Larakit\SEO\Schema\SchemaContext;
use Therajatspace\Larakit\SEO\Schema\SchemaManager;
use Therajatspace\Larakit\SEO\Schema\SchemaRelationshipResolver;
use Therajatspace\Larakit\SEO\Schema\WebSiteSchema;

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

    /*
    |--------------------------------------------------------------------------
    | Publisher
    |--------------------------------------------------------------------------
    */

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

    public function test_publisher_accepts_organization_schema(): void
    {
        $organization = new OrganizationSchema();

        $organization
            ->id('https://example.com/#organization')
            ->name('LaraKit');

        $website = new WebSiteSchema();

        $data = $website
            ->publisher($organization)
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $data['publisher']
        );
    }

    public function test_publisher_rejects_organization_without_id(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $organization = new OrganizationSchema();

        $organization->name('LaraKit');

        $website = new WebSiteSchema();

        $website->publisher($organization);
    }

    public function test_publisher_is_fluent_with_organization_schema(): void
    {
        $organization = new OrganizationSchema();

        $organization
            ->id('https://example.com/#organization')
            ->name('LaraKit');

        $website = new WebSiteSchema();

        $this->assertSame(
            $website,
            $website->publisher($organization)
        );
    }

    public function test_publisher_can_be_replaced(): void
    {
        $first = new OrganizationSchema();

        $first
            ->id('https://example.com/#first-organization')
            ->name('First Organization');

        $second = new OrganizationSchema();

        $second
            ->id('https://example.com/#second-organization')
            ->name('Second Organization');

        $website = new WebSiteSchema();

        $website->publisher($first);
        $website->publisher($second);

        $this->assertSame(
            [
                '@id' => 'https://example.com/#second-organization',
            ],
            $website->toArray()['publisher']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | fromArray
    |--------------------------------------------------------------------------
    */

    public function test_from_array_accepts_string_publisher(): void
    {
        $website = new WebSiteSchema();

        $data = $website
            ->fromArray([
                'name' => 'LaraKit',
                'publisher' => 'https://example.com/#organization',
            ])
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $data['publisher']
        );
    }

    public function test_from_array_accepts_organization_schema_as_publisher(): void
    {
        $organization = new OrganizationSchema();

        $organization
            ->id('https://example.com/#organization')
            ->name('LaraKit');

        $website = new WebSiteSchema();

        $data = $website
            ->fromArray([
                'name' => 'LaraKit',
                'publisher' => $organization,
            ])
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $data['publisher']
        );
    }

    public function test_from_array_rejects_organization_without_id(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $organization = new OrganizationSchema();

        $organization->name('LaraKit');

        $website = new WebSiteSchema();

        $website->fromArray([
            'publisher' => $organization,
        ]);
    }

    public function test_from_array_preserves_website_type(): void
    {
        $website = new WebSiteSchema();

        $data = $website
            ->fromArray([
                'name' => 'LaraKit',
            ])
            ->toArray();

        $this->assertSame(
            'WebSite',
            $data['@type']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reference integrity
    |--------------------------------------------------------------------------
    */

    public function test_publisher_reference_does_not_have_context(): void
    {
        $organization = new OrganizationSchema();

        $organization
            ->id('https://example.com/#organization')
            ->name('LaraKit');

        $website = new WebSiteSchema();

        $data = $website
            ->publisher($organization)
            ->toArray();

        $this->assertArrayNotHasKey(
            '@context',
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