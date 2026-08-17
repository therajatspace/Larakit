<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\SchemaGraph;
use Therajatspace\Larakit\SEO\Schema\OrganizationSchema;
use Therajatspace\Larakit\SEO\Schema\WebSiteSchema;

class SchemaGraphTest extends TestCase
{
    public function test_graph_contains_context_and_graph(): void
    {
        $graph = new SchemaGraph();

        $organization = new OrganizationSchema();

        $graph->add($organization);

        $data = $graph->toArray();

        $this->assertSame(
            'https://schema.org',
            $data['@context']
        );

        $this->assertCount(
            1,
            $data['@graph']
        );
    }

    public function test_graph_can_contain_multiple_entities(): void
    {
        $graph = new SchemaGraph();

        $organization = new OrganizationSchema();
        $website = new WebSiteSchema();

        $graph
            ->add($organization)
            ->add($website);

        $this->assertCount(
            2,
            $graph->toArray()['@graph']
        );
    }

    public function test_graph_preserves_entity_ids(): void
    {
        $graph = new SchemaGraph();

        $organization = new OrganizationSchema();

        $organization
            ->id('https://example.com/#organization')
            ->name('LaraKit');

        $graph->add($organization);

        $data = $graph->toArray();

        $this->assertSame(
            'https://example.com/#organization',
            $data['@graph'][0]['@id']
        );
    }

    public function test_graph_can_render_json_ld(): void
    {
        $graph = new SchemaGraph();

        $organization = new OrganizationSchema();

        $organization
            ->id('https://example.com/#organization')
            ->name('LaraKit');

        $graph->add($organization);

        $result = $graph->render();

        $this->assertStringContainsString(
            'application/ld+json',
            $result
        );

        $this->assertStringContainsString(
            '"@graph"',
            $result
        );

        $this->assertStringContainsString(
            '"@id":"https://example.com/#organization"',
            $result
        );
    }
    public function test_graph_can_find_schema_by_type(): void
    {
        $graph = new SchemaGraph();

        $organization = new OrganizationSchema();

        $graph->add($organization);

        $this->assertSame(
            $organization,
            $graph->findByType('Organization')
        );
    }

    public function test_graph_returns_null_when_type_does_not_exist(): void
    {
        $graph = new SchemaGraph();

        $this->assertNull(
            $graph->findByType('Organization')
        );
    }
}