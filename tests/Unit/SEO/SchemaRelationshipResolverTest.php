<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\OrganizationSchema;
use Therajatspace\Larakit\SEO\Schema\SchemaGraph;
use Therajatspace\Larakit\SEO\Schema\SchemaRelationshipResolver;
use Therajatspace\Larakit\SEO\Schema\WebSiteSchema;
use Therajatspace\Larakit\SEO\Schema\ArticleSchema;

class SchemaRelationshipResolverTest extends TestCase
{
    protected function createGraph(): SchemaGraph
    {
        return new SchemaGraph();
    }

    public function test_website_is_connected_to_organization(): void
    {
        $graph = $this->createGraph();

        $organization = new OrganizationSchema();
        $organization
            ->id('https://example.com/#organization')
            ->name('LaraKit');

        $website = new WebSiteSchema();
        $website
            ->id('https://example.com/#website')
            ->name('LaraKit');

        $graph
            ->add($organization)
            ->add($website);

        $resolver = new SchemaRelationshipResolver();

        $resolver->resolve($graph);

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $website->toArray()['publisher']
        );
    }

    public function test_article_is_connected_to_organization(): void
    {
        $graph = $this->createGraph();

        $organization = new OrganizationSchema();
        $organization->id(
            'https://example.com/#organization'
        );

        $article = new ArticleSchema();
        $article->id(
            'https://example.com/article/#article'
        );

        $graph
            ->add($organization)
            ->add($article);

        $resolver = new SchemaRelationshipResolver();

        $resolver->resolve($graph);

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $article->toArray()['publisher']
        );
    }

    public function test_article_is_connected_to_website(): void
    {
        $graph = $this->createGraph();

        $website = new WebSiteSchema();
        $website->id(
            'https://example.com/#website'
        );

        $article = new ArticleSchema();
        $article->id(
            'https://example.com/article/#article'
        );

        $graph
            ->add($website)
            ->add($article);

        $resolver = new SchemaRelationshipResolver();

        $resolver->resolve($graph);

        $this->assertSame(
            [
                '@id' => 'https://example.com/#website',
            ],
            $article->toArray()['isPartOf']
        );
    }

    public function test_entities_without_ids_are_not_connected(): void
    {
        $graph = $this->createGraph();

        $organization = new OrganizationSchema();
        $organization->name('LaraKit');

        $article = new ArticleSchema();
        $article->name('LaraKit Article');

        $graph
            ->add($organization)
            ->add($article);

        $resolver = new SchemaRelationshipResolver();

        $resolver->resolve($graph);

        $this->assertArrayNotHasKey(
            'publisher',
            $article->toArray()
        );
    }
}