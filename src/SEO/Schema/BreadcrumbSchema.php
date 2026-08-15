<?php

namespace Sidd2604\Larakit\SEO\Schema;
use Sidd2604\Larakit\SEO\Support\UrlValidator;

class BreadcrumbSchema extends SchemaObject
{
    protected array $items = [];

    public function __construct()
    {
        $this->data['@type'] = 'BreadcrumbList';
    }

    public function item(string $name, string $url): static
    {
        UrlValidator::validate($url);

        $this->items[] = [
            '@type' => 'ListItem',
            'position' => count($this->items) + 1,
            'name' => $name,
            'item' => $url,
        ];

        $this->data['itemListElement'] = $this->items;

        return $this;
    }
}