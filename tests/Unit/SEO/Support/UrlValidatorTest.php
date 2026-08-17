<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO\Support;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Support\UrlValidator;

class UrlValidatorTest extends TestCase
{
    public function test_valid_url_is_accepted(): void
    {
        UrlValidator::validate(
            'https://example.com/article'
        );

        $this->expectNotToPerformAssertions();
    }

    public function test_invalid_url_throws_exception(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        UrlValidator::validate('banana');
    }
}