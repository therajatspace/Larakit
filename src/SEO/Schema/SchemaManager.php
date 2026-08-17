<?php

namespace Sidd2604\Larakit\SEO\Schema;
use Sidd2604\Larakit\SEO\Schema\BreadcrumbSchema;
use Sidd2604\Larakit\SEO\Schema\OrganizationSchema;
use Sidd2604\Larakit\SEO\Schema\WebSiteSchema;
use Sidd2604\Larakit\SEO\Schema\ProductSchema;
use Sidd2604\Larakit\SEO\Schema\SchemaObject;
use Sidd2604\Larakit\SEO\Schema\SchemaRelationshipResolver;

class SchemaManager
{
    protected SchemaGraph $graph;
    protected SchemaContext $context;
    protected SchemaRelationshipResolver $relationshipResolver;

    // /**
    //  * @template T of SchemaObject
    //  *
    //  * @param class-string<T> $class
    //  *
    //  * @return T
    //  */

    public function __construct(
        SchemaContext $context,
        SchemaRelationshipResolver $relationshipResolver
    ) {
        $this->context = $context;
        $this->graph = new SchemaGraph();
        $this->relationshipResolver = $relationshipResolver;
    }

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

        $this->graph->add($schema);

        return $schema;
    }

    public function article(array $data = []): ArticleSchema
    {
        return $this
            ->create(ArticleSchema::class)
            ->id($this->pageId('article'))
            ->fromArray($data);
    }
    public function breadcrumbs(array $data = []): BreadcrumbSchema
    {
        return $this
            ->create(BreadcrumbSchema::class)
            ->id($this->pageId('breadcrumb'))
            ->fromArray($data);
    }
    public function organization(array $data = []): OrganizationSchema
    {
        return $this
            ->create(OrganizationSchema::class)
            ->id($this->id('organization'))
            ->fromArray($data);
    }

    public function website(array $data = []): WebSiteSchema
    {
        return $this
            ->create(WebSiteSchema::class)
            ->id($this->id('website'))
            ->fromArray($data);
    }

    public function product(array $data = []): ProductSchema
    {
        return $this
            ->create(ProductSchema::class)
            ->id($this->pageId('product'))
            ->fromArray($data);
    }

    public function count(): int
    {
        return count($this->graph->all());
    }


    public function id(string $fragment): string
    {
        return $this->context->id($fragment);
    }
    public function pageId(string $fragment): string
    {
        return $this->context->id($fragment, true);
    }


    public function connect(
        SchemaObject $from,
        string $property,
        SchemaObject $to
    ): static {
        $target = $to->toArray();

        if (!isset($target['@id'])) {
            throw new \InvalidArgumentException(
                'The target schema must have an @id.'
            );
        }

        $fromData = $from->toArray();

        if (isset($fromData[$property])) {
            return $this;
        }

        $from->property(
            $property,
            [
                '@id' => $target['@id'],
            ]
        );

        return $this;
    }
    public function findByType(string $type): ?SchemaObject
    {
        foreach ($this->graph->all() as $schema) {
            if (($schema->toArray()['@type'] ?? null) === $type) {
                return $schema;
            }
        }

        return null;
    }
    protected function buildAutomaticRelationships(): void
    {
        $organization = $this->findByType('Organization');
        $website = $this->findByType('WebSite');
        $article = $this->findByType('Article');

        if (
            $organization &&
            $website &&
            isset($organization->toArray()['@id']) &&
            isset($website->toArray()['@id'])
        ) {
            $this->connect(
                $website,
                'publisher',
                $organization
            );
        }

        if (
            $organization &&
            $article &&
            isset($organization->toArray()['@id']) &&
            isset($article->toArray()['@id'])
        ) {
            $this->connect(
                $article,
                'publisher',
                $organization
            );
        }

        if (
            $website &&
            $article &&
            isset($website->toArray()['@id']) &&
            isset($article->toArray()['@id'])
        ) {
            $this->connect(
                $article,
                'isPartOf',
                $website
            );
        }
    }

    public function render(): string
    {
        $this->relationshipResolver->resolve($this->graph);

        return $this->graph->render();
    }
}