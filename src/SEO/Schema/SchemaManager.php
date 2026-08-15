<?php

namespace Sidd2604\Larakit\SEO\Schema;
use Sidd2604\Larakit\SEO\Schema\BreadcrumbSchema;
use Sidd2604\Larakit\SEO\Schema\OrganizationSchema;
use Sidd2604\Larakit\SEO\Schema\WebSiteSchema;
use Sidd2604\Larakit\SEO\Schema\ProductSchema;

class SchemaManager
{
    protected array $schemas = [];

    /**
     * @template T of SchemaObject
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function create(string $class = SchemaObject::class): SchemaObject
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(
                "Schema class [{$class}] does not exist."
            );
        }

        if (!is_a($class, SchemaObject::class, true)) {
            throw new \InvalidArgumentException(
                "Schema class [{$class}] must extend " . SchemaObject::class
            );
        }

        $schema = new $class();

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
        return $this
            ->create(OrganizationSchema::class)
            ->fromArray($data);
    }

    public function website(array $data = []): WebSiteSchema
    {
        return $this
            ->create(WebSiteSchema::class)
            ->fromArray($data);
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