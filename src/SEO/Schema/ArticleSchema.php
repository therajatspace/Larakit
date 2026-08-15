<?php

namespace Sidd2604\Larakit\SEO\Schema;
use Sidd2604\Larakit\SEO\Support\UrlValidator;
use Sidd2604\Larakit\SEO\Support\DateValidator;

class ArticleSchema extends SchemaObject
{
    public function __construct()
    {
        $this->data['@type'] = 'Article';
    }

    public function headline(string $headline): static
    {
        $this->data['headline'] = $headline;

        return $this;
    }

    public function author(string $name): static
    {
        $this->data['author'] = [
            '@type' => 'Person',
            'name' => $name,
        ];

        return $this;
    }

    public function datePublished(string $date): static
    {
        DateValidator::validate($date);

        $this->data['datePublished'] = $date;

        return $this;
    }

    public function dateModified(string $date): static
    {
        DateValidator::validate($date);

        $this->data['dateModified'] = $date;

        return $this;
    }

    public function image(string $url): static
    {
        UrlValidator::validate($url);

        $this->data['image'] = $url;

        return $this;
    }
}