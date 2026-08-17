<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\SchemaContext;

class SchemaContextTest extends TestCase
{
    protected SchemaContext $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = new SchemaContext(
            'https://example.com/',
            'https://example.com/blog/laravel'
        );
    }

    public function test_base_url_is_normalized(): void
    {
        $this->assertSame(
            'https://example.com',
            $this->context->baseUrl()
        );
    }

    public function test_current_url_is_available(): void
    {
        $this->assertSame(
            'https://example.com/blog/laravel',
            $this->context->currentUrl()
        );
    }

    public function test_global_id_uses_base_url(): void
    {
        $this->assertSame(
            'https://example.com/#organization',
            $this->context->id('organization')
        );
    }

    public function test_current_page_id_uses_current_url(): void
    {
        $this->assertSame(
            'https://example.com/blog/laravel/#article',
            $this->context->id('article', true)
        );
    }

    public function test_leading_hash_is_removed(): void
    {
        $this->assertSame(
            'https://example.com/#website',
            $this->context->id('#website')
        );

        $this->assertSame(
            'https://example.com/blog/laravel/#article',
            $this->context->id('#article', true)
        );
    }
}