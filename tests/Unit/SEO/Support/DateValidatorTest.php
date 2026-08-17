<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO\Support;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Support\DateValidator;

class DateValidatorTest extends TestCase
{
    public function test_full_date_is_accepted(): void
    {
        DateValidator::validate('2026-08-15');

        $this->expectNotToPerformAssertions();
    }

    public function test_year_and_month_are_accepted(): void
    {
        DateValidator::validate('2026-08');
        DateValidator::validate('2026');

        $this->expectNotToPerformAssertions();
    }

    public function test_datetime_is_accepted(): void
    {
        DateValidator::validate(
            '2026-08-15T21:30:00+05:30'
        );

        $this->expectNotToPerformAssertions();
    }

    public function test_invalid_date_throws_exception(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        DateValidator::validate('banana');
    }
}