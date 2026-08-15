<?php

namespace Sidd2604\Larakit\SEO\Support;

class UrlValidator
{
    public static function validate(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(
                "Invalid URL: {$url}"
            );
        }
    }
}