<?php

namespace Therajatspace\Larakit\SEO\Schema;

class SchemaReference
{
    public function __construct(
        protected string $id
    ) {
    }

    public function toArray(): array
    {
        return [
            '@id' => $this->id,
        ];
    }
}