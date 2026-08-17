<?php

namespace Therajatspace\Larakit\SEO\Schema;

class SchemaConfigurator
{
    public function __construct(
        protected SchemaManager $schema
    ) {
    }

    public function configure(array $config): void
    {
        if (($config['schema']['auto'] ?? true) === false) {
            return;
        }

        $organization = $config['organization'] ?? [];

        if (! empty($organization['name'])) {
            $this->schema->organization($organization);
        }

        $website = $config['website'] ?? [];

        if (! empty($website['name'])) {
            $this->schema->website($website);
        }
    }
}