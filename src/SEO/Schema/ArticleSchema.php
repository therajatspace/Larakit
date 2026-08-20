<?php

namespace Therajatspace\Larakit\SEO\Schema;
use Therajatspace\Larakit\SEO\Support\UrlValidator;
use Therajatspace\Larakit\SEO\Support\DateValidator;
use Therajatspace\Larakit\SEO\Schema\OrganizationSchema;

class ArticleSchema extends SchemaObject
{
    public function __construct()
    {
        $this->data['@type'] = 'Article';
    }

    public function headline(string $headline): static
    {
        $this->data['headline'] = $headline;

        return $this;
    }

    public function author(string|PersonSchema $author): static
    {
        if ($author instanceof PersonSchema) {
            if (!$author->hasId()) {
                throw new \InvalidArgumentException(
                    'The author PersonSchema must have an @id.'
                );
            }

            $this->data['author'] = [
                '@id' => $author->toArray()['@id'],
            ];

            return $this;
        }

        $this->data['author'] = [
            '@type' => 'Person',
            'name' => $author,
        ];

        return $this;
    }

    public function datePublished(string $date): static
    {
        DateValidator::validate($date);

        $this->data['datePublished'] = $date;

        return $this;
    }

    public function dateModified(string $date): static
    {
        DateValidator::validate($date);

        $this->data['dateModified'] = $date;

        return $this;
    }

    public function image(string $url): static
    {
        UrlValidator::validate($url);

        $this->data['image'] = $url;

        return $this;
    }
    public function fromArray(array $data): static
    {
        if (isset($data['name'])) {
            $this->name($data['name']);
        }

        if (isset($data['description'])) {
            $this->description($data['description']);
        }

        if (isset($data['url'])) {
            $this->url($data['url']);
        }

        if (isset($data['headline'])) {
            $this->headline($data['headline']);
        }

        if (isset($data['author'])) {
            $this->author($data['author']);
        }

        if (isset($data['publisher'])) {
            $this->publisher($data['publisher']);
        }

        if (isset($data['datePublished'])) {
            $this->datePublished($data['datePublished']);
        }

        if (isset($data['dateModified'])) {
            $this->dateModified($data['dateModified']);
        }

        if (isset($data['image'])) {
            $this->image($data['image']);
        }

        if (isset($data['isPartOf'])) {
            $this->isPartOf($data['isPartOf']);
        }

        return $this;
    }
    public function publisher(string|OrganizationSchema $publisher): static
    {
        if ($publisher instanceof OrganizationSchema) {
            if (!$publisher->hasId()) {
                throw new \InvalidArgumentException(
                    'The publisher OrganizationSchema must have an @id.'
                );
            }

            $this->data['publisher'] = [
                '@id' => $publisher->toArray()['@id'],
            ];

            return $this;
        }

        $this->data['publisher'] = [
            '@id' => $publisher,
        ];

        return $this;
    }

    public function isPartOf(string|SchemaObject $entity): static
    {
        if ($entity instanceof SchemaObject) {
            if (!$entity->hasId()) {
                throw new \InvalidArgumentException(
                    'The isPartOf SchemaObject must have an @id.'
                );
            }

            $this->data['isPartOf'] = [
                '@id' => $entity->toArray()['@id'],
            ];

            return $this;
        }

        $this->data['isPartOf'] = [
            '@id' => $entity,
        ];

        return $this;
    }
}