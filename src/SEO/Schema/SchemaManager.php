<?php

namespace Sidd2604\Larakit\SEO\Schema;
use Sidd2604\Larakit\SEO\Schema\BreadcrumbSchema;
use Sidd2604\Larakit\SEO\Schema\OrganizationSchema;
use Sidd2604\Larakit\SEO\Schema\WebSiteSchema;

class SchemaManager
{
    protected array $schemas = [];

    public function create(): SchemaObject
    {
        $schema = new SchemaObject();

        $this->schemas[] = $schema;

        return $schema;
    }

    public function article(): ArticleSchema
    {
        $schema = new ArticleSchema();

        $this->schemas[] = $schema;

        return $schema;
    }
    public function breadcrumbs(): BreadcrumbSchema
    {
        $schema = new BreadcrumbSchema();

        $this->schemas[] = $schema;

        return $schema;
    }
    public function organization(array $data = []): OrganizationSchema
    {
        $schema = new OrganizationSchema();

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

        $this->schemas[] = $schema;

        return $schema;
    }
    public function website(array $data = []): WebSiteSchema
    {
        $schema = new WebSiteSchema();

        if (isset($data['name'])) {
            $schema->name($data['name']);
        }

        if (isset($data['url'])) {
            $schema->url($data['url']);
        }

        if (isset($data['description'])) {
            $schema->description($data['description']);
        }

        $this->schemas[] = $schema;

        return $schema;
    }

    public function count(): int
    {
        return count($this->schemas);
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