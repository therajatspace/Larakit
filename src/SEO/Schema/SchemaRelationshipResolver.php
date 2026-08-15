<?php

namespace Sidd2604\Larakit\SEO\Schema;

class SchemaRelationshipResolver
{
    public function resolve(SchemaGraph $graph): void
    {
        $organization = $graph->findByType('Organization');

        $website = $graph->findByType('WebSite');

        $article = $graph->findByType('Article');

        if (
            $organization &&
            $website &&
            $organization->hasId() &&
            $website->hasId()
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
            $organization->hasId() &&
            $article->hasId()
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
            $website->hasId() &&
            $article->hasId()
        ) {
            $this->connect(
                $article,
                'isPartOf',
                $website
            );
        }
    }


    protected function connect(
        SchemaObject $from,
        string $property,
        SchemaObject $to
    ): void {
        $target = $to->toArray();

        if (! isset($target['@id'])) {
            return;
        }

        $fromData = $from->toArray();

        if (isset($fromData[$property])) {
            return;
        }

        $from->property(
            $property,
            [
                '@id' => $target['@id'],
            ]
        );
    }
}