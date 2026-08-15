<?php

namespace Sidd2604\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Sidd2604\Larakit\SEO\Schema\WebSiteSchema;

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

        $this->assertSame('LaraKit', $data['name']);

        $this->assertSame(
            'Laravel SEO toolkit',
            $data['description']
        );

        $this->assertSame(
            'https://example.com',
            $data['url']
        );
    }
}