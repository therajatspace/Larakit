<?php

namespace Therajatspace\Larakit\SEO\Schema;

use Therajatspace\Larakit\SEO\Support\DateValidator;
use Therajatspace\Larakit\SEO\Support\UrlValidator;
use Therajatspace\Larakit\SEO\Schema\WebSiteSchema;
use Therajatspace\Larakit\SEO\Schema\BreadcrumbSchema;

class WebPageSchema extends SchemaObject
{
    public function __construct()
    {
        $this->data['@type'] = 'WebPage';
    }

    public function image(string $url): static
    {
        UrlValidator::validate($url);

        $this->data['image'] = $url;

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

    public function inLanguage(string $language): static
    {
        $this->data['inLanguage'] = $language;

        return $this;
    }

    public function isPartOf(string|WebSiteSchema $website): static
    {
        if ($website instanceof WebSiteSchema) {
            if (!$website->hasId()) {
                throw new \InvalidArgumentException(
                    'The isPartOf WebSiteSchema must have an @id.'
                );
            }

            $this->data['isPartOf'] = [
                '@id' => $website->toArray()['@id'],
            ];

            return $this;
        }

        $this->data['isPartOf'] = [
            '@id' => $website,
        ];

        return $this;
    }

    public function mainEntity(string|SchemaObject $entity): static
    {
        if ($entity instanceof SchemaObject) {
            if (!$entity->hasId()) {
                throw new \InvalidArgumentException(
                    'The mainEntity SchemaObject must have an @id.'
                );
            }

            $this->data['mainEntity'] = [
                '@id' => $entity->toArray()['@id'],
            ];

            return $this;
        }

        $this->data['mainEntity'] = [
            '@id' => $entity,
        ];

        return $this;
    }
    public function primaryImageOfPage(string $id): static
    {
        $this->data['primaryImageOfPage'] = [
            '@id' => $id,
        ];

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

        if (isset($data['image'])) {
            $this->image($data['image']);
        }

        if (isset($data['datePublished'])) {
            $this->datePublished($data['datePublished']);
        }

        if (isset($data['dateModified'])) {
            $this->dateModified($data['dateModified']);
        }

        if (isset($data['inLanguage'])) {
            $this->inLanguage($data['inLanguage']);
        }

        if (isset($data['isPartOf'])) {
            $this->isPartOf($data['isPartOf']);
        }

        if (isset($data['mainEntity'])) {
            $this->mainEntity($data['mainEntity']);
        }

        if (isset($data['primaryImageOfPage'])) {
            $this->primaryImageOfPage(
                $data['primaryImageOfPage']
            );
        }

        if (isset($data['breadcrumb'])) {
            $this->breadcrumb($data['breadcrumb']);
        }

        return $this;
    }

    public function breadcrumb(string|BreadcrumbSchema $breadcrumb): static
    {
        if ($breadcrumb instanceof BreadcrumbSchema) {
            if (!$breadcrumb->hasId()) {
                throw new \InvalidArgumentException(
                    'The breadcrumb BreadcrumbSchema must have an @id.'
                );
            }

            $this->data['breadcrumb'] = [
                '@id' => $breadcrumb->toArray()['@id'],
            ];

            return $this;
        }

        $this->data['breadcrumb'] = [
            '@id' => $breadcrumb,
        ];

        return $this;
    }
}