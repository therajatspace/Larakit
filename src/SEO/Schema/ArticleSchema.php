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
    public function fromArray(array $data): static
    {
        if (isset($data['name'])) {
            $this->name($data['name']);
        }

        if (isset($data['description'])) {
            $this->description($data['description']);
        }

        if (isset($data['url'])) {
            $this->url($data['url']);
        }

        if (isset($data['headline'])) {
            $this->headline($data['headline']);
        }

        if (isset($data['author'])) {
            $this->author($data['author']);
        }

        if (isset($data['datePublished'])) {
            $this->datePublished($data['datePublished']);
        }

        if (isset($data['dateModified'])) {
            $this->dateModified($data['dateModified']);
        }

        if (isset($data['image'])) {
            $this->image($data['image']);
        }

        return $this;
    }
}