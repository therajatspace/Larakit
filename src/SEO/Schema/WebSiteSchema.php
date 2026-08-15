<?php

namespace Sidd2604\Larakit\SEO\Schema;

class WebSiteSchema extends SchemaObject
{
    public function __construct()
    {
        $this->data['@type'] = 'WebSite';
    }
}