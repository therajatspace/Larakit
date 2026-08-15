<?php

namespace Sidd2604\Larakit\SEO\Schema;

use Sidd2604\Larakit\SEO\Support\UrlValidator;

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
}