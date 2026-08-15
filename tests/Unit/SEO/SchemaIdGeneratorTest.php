<?php

namespace Sidd2604\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Sidd2604\Larakit\SEO\Schema\SchemaIdGenerator;

class SchemaIdGeneratorTest extends TestCase
{
    public function test_it_generates_id_from_base_url(): void
    {
        $generator = new SchemaIdGenerator(
            'https://example.com'
        );

        $this->assertSame(
            'https://example.com/#organization',
            $generator->generate('organization')
        );
    }

    public function test_it_removes_trailing_slash(): void
    {
        $generator = new SchemaIdGenerator(
            'https://example.com/'
        );

        $this->assertSame(
            'https://example.com/#website',
            $generator->generate('website')
        );
    }

    public function test_it_removes_leading_hash(): void
    {
        $generator = new SchemaIdGenerator(
            'https://example.com'
        );

        $this->assertSame(
            'https://example.com/#article',
            $generator->generate('#article')
        );
    }
}