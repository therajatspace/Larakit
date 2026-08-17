<?php

namespace Therajatspace\Larakit\SEO\Schema;

class SchemaIdGenerator
{
    public function __construct(
        protected string $baseUrl
    ) {
    }

    public function generate(string $fragment): string
    {
        return rtrim($this->baseUrl, '/')
            . '/#'
            . ltrim($fragment, '#');
    }
}