<?php

namespace Therajatspace\Larakit\SEO\Schema;

use Therajatspace\Larakit\SEO\Support\UrlValidator;

class OrganizationSchema extends SchemaObject
{
    public function __construct()
    {
        $this->data['@type'] = 'Organization';
    }

    public function logo(string $url): static
    {
        UrlValidator::validate($url);

        $this->data['logo'] = $url;

        return $this;
    }

    public function sameAs(array $urls): static
    {
        foreach ($urls as $url) {
            UrlValidator::validate($url);
        }

        $this->data['sameAs'] = $urls;

        return $this;
    }
    public function fromArray(array $data): static
    {
        if (isset($data['name'])) {
            $this->name($data['name']);
        }

        if (isset($data['url'])) {
            $this->url($data['url']);
        }

        if (isset($data['logo'])) {
            $this->logo($data['logo']);
        }

        if (!empty($data['same_as'])) {
            $this->sameAs($data['same_as']);
        }

        return $this;
    }
}