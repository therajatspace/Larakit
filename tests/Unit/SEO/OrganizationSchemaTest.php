<?php

namespace Sidd2604\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Sidd2604\Larakit\SEO\Schema\OrganizationSchema;

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
}