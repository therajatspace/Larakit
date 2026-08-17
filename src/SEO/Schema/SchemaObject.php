<?php

namespace Therajatspace\Larakit\SEO\Schema;

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
    public function fromArray(array $data): static
    {
        foreach ($data as $property => $value) {
            $this->data[$property] = $value;
        }

        return $this;
    }
    public function id(string $id): static
    {
        $this->data['@id'] = $id;

        return $this;
    }
    public function reference(string $id): array
    {
        return [
            '@id' => $id,
        ];
    }
    public function ref(string $id): SchemaReference
    {
        return new SchemaReference($id);
    }
    public function hasId(): bool
    {
        return isset($this->data['@id']);
    }
}