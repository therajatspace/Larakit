<?php

namespace Therajatspace\Larakit\SEO\Support;

use DateTimeImmutable;

class DateValidator
{
    protected const FORMATS = [
        'Y',
        'Y-m',
        'Y-m-d',
        DATE_ATOM,
    ];

    public static function validate(string $date): void
    {
        foreach (self::FORMATS as $format) {
            $parsed = DateTimeImmutable::createFromFormat(
                $format,
                $date
            );

            if (
                $parsed !== false &&
                $parsed->format($format) === $date
            ) {
                return;
            }
        }

        throw new \InvalidArgumentException(
            "Invalid date: {$date}"
        );
    }
}