<?php

namespace Sidd2604\Larakit\SEO\Schema;
use Sidd2604\Larakit\SEO\Schema\BreadcrumbSchema;
use Sidd2604\Larakit\SEO\Schema\OrganizationSchema;
use Sidd2604\Larakit\SEO\Schema\WebSiteSchema;
use Sidd2604\Larakit\SEO\Schema\ProductSchema;

class SchemaManager
{
    protected array $schemas = [];

    public function create(string $class = SchemaObject::class): SchemaObject
    {
        $schema = new $class();

        if (!$schema instanceof SchemaObject) {
            throw new \InvalidArgumentException(
                "{$class} must extend " . SchemaObject::class
            );
        }

        $this->schemas[] = $schema;

        return $schema;
    }

    public function article(): ArticleSchema
    {
        return $this->create(ArticleSchema::class);
    }
    public function breadcrumbs(): BreadcrumbSchema
    {
        return $this->create(BreadcrumbSchema::class);
    }
    public function organization(array $data = []): OrganizationSchema
    {
        $schema = $this->create(OrganizationSchema::class);

        if (isset($data['name'])) {
            $schema->name($data['name']);
        }

        if (isset($data['url'])) {
            $schema->url($data['url']);
        }

        if (isset($data['logo'])) {
            $schema->logo($data['logo']);
        }

        if (!empty($data['same_as'])) {
            $schema->sameAs($data['same_as']);
        }

        return $schema;
    }
    public function website(array $data = []): WebSiteSchema
    {
        $schema = $this->create(WebSiteSchema::class);

        if (isset($data['name'])) {
            $schema->name($data['name']);
        }

        if (isset($data['url'])) {
            $schema->url($data['url']);
        }

        if (isset($data['description'])) {
            $schema->description($data['description']);
        }

        return $schema;
    }

    public function count(): int
    {
        return count($this->schemas);
    }
    public function product(): ProductSchema
    {
        return $this->create(ProductSchema::class);
    }

    public function render(): string
    {
        $html = '';

        foreach ($this->schemas as $schema) {
            $html .= '<script type="application/ld+json">'
                . json_encode(
                    $schema->toArray(),
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                )
                . '</script>';
        }

        return $html;
    }
}