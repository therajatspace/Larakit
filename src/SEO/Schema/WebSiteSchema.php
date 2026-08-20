<?php

namespace Therajatspace\Larakit\SEO\Schema;

class WebSiteSchema extends SchemaObject
{
    public function __construct()
    {
        $this->data['@type'] = 'WebSite';
    }

    public function fromArray(array $data): static
    {
        if (isset($data['name'])) {
            $this->name($data['name']);
        }

        if (isset($data['url'])) {
            $this->url($data['url']);
        }

        if (isset($data['description'])) {
            $this->description($data['description']);
        }

        if (isset($data['publisher'])) {
            $this->publisher($data['publisher']);
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
}