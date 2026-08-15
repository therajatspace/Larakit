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
    public function organization(): OrganizationSchema
    {
        $schema = new OrganizationSchema();

        $this->schemas[] = $schema;

        return $schema;
    }
    public function website(): WebSiteSchema
    {
        $schema = new WebSiteSchema();

        $this->schemas[] = $schema;

        return $schema;
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