<?php

namespace Sidd2604\Larakit\SEO\Schema;

class SchemaObject
{
    protected array $data = [
        '@context' => 'https://schema.org',
    ];

    public function type(string $type): static
    {
        $this->data['@type'] = $type;

        return $this;
    }

    public function name(string $name): static
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function description(string $description): static
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function url(string $url): static
    {
        $this->data['url'] = $url;

        return $this;
    }

    public function property(string $key, mixed $value): static
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}