<?php

namespace Sidd2604\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Sidd2604\Larakit\SEO\Schema\OrganizationSchema;
use Sidd2604\Larakit\SEO\Schema\SchemaManager;
use Sidd2604\Larakit\SEO\Schema\SchemaContext;
use Sidd2604\Larakit\SEO\Schema\SchemaRelationshipResolver;

class OrganizationSchemaTest extends TestCase
{
    public function test_organization_has_correct_type(): void
    {
        $organization = new OrganizationSchema();

        $this->assertSame(
            'Organization',
            $organization->toArray()['@type']
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

    public function test_logo_is_stored(): void
    {
        $organization = new OrganizationSchema();

        $data = $organization
            ->logo('https://example.com/logo.png')
            ->toArray();

        $this->assertSame(
            'https://example.com/logo.png',
            $data['logo']
        );
    }

    public function test_same_as_urls_are_stored(): void
    {
        $organization = new OrganizationSchema();

        $urls = [
            'https://github.com/example',
            'https://linkedin.com/company/example',
        ];

        $data = $organization
            ->sameAs($urls)
            ->toArray();

        $this->assertSame(
            $urls,
            $data['sameAs']
        );
    }

    public function test_invalid_logo_url_throws_exception(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $organization = new OrganizationSchema();

        $organization->logo('banana');
    }

    public function test_invalid_same_as_url_throws_exception(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $organization = new OrganizationSchema();

        $organization->sameAs([
            'https://github.com/example',
            'banana',
        ]);
    }

    public function test_organization_can_be_created_from_data(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
            'url' => 'https://example.com',
            'logo' => 'https://example.com/logo.png',
            'same_as' => [
                'https://github.com/example',
            ],
        ]);

        $data = $organization->toArray();

        $this->assertSame(
            'Organization',
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
            'https://example.com/logo.png',
            $data['logo']
        );

        $this->assertSame(
            ['https://github.com/example'],
            $data['sameAs']
        );
    }
}