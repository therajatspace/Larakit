<?php

namespace Sidd2604\Larakit\SEO\Schema;

class SchemaGraph
{
    protected array $schemas = [];

    public function add(SchemaObject $schema): static
    {
        $this->schemas[] = $schema;

        return $this;
    }

    public function all(): array
    {
        return $this->schemas;
    }

    public function toArray(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => array_map(
                fn(SchemaObject $schema) => $schema->toArray(),
                $this->schemas
            ),
        ];
    }

    public function render(): string
    {
        return '<script type="application/ld+json">'
            . json_encode(
                $this->toArray(),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
            . '</script>';
    }
    public function findByType(string $type): ?SchemaObject
    {
        foreach ($this->schemas as $schema) {
            if (($schema->toArray()['@type'] ?? null) === $type) {
                return $schema;
            }
        }

        return null;
    }
}