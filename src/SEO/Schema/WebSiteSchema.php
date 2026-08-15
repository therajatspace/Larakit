<?php

namespace Sidd2604\Larakit\SEO\Schema;

class WebSiteSchema extends SchemaObject
{
    public function __construct()
    {
        $this->data['@type'] = 'WebSite';
    }
    public function fromArray(array $data): static
    {
        if (isset($data['name'])) {
            $this->name($data['name']);
        }

        if (isset($data['url'])) {
            $this->url($data['url']);
        }

        if (isset($data['description'])) {
            $this->description($data['description']);
        }

        return $this;
    }
    public function publisher(string $id): static
    {
        $this->data['publisher'] = [
            '@id' => $id,
        ];

        return $this;
    }
}