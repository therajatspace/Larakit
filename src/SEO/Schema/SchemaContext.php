<?php

namespace Sidd2604\Larakit\SEO\Schema;

class SchemaContext
{
    public function __construct(
        protected string $baseUrl,
        protected string $currentUrl
    ) {
    }

    public function baseUrl(): string
    {
        return rtrim($this->baseUrl, '/');
    }

    public function currentUrl(): string
    {
        return $this->currentUrl;
    }

    public function id(string $fragment, bool $currentPage = false): string
    {
        $url = $currentPage
            ? $this->currentUrl()
            : $this->baseUrl();

        return rtrim($url, '/')
            . '/#'
            . ltrim($fragment, '#');
    }
}