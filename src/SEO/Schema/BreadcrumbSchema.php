<?php

namespace Therajatspace\Larakit\SEO\Schema;
use Therajatspace\Larakit\SEO\Support\UrlValidator;

class BreadcrumbSchema extends SchemaObject
{
    protected array $items = [];

    public function __construct()
    {
        $this->data['@type'] = 'BreadcrumbList';
    }

    public function item(string $name, string $url): static
    {
        UrlValidator::validate($url);

        $this->items[] = [
            '@type' => 'ListItem',
            'position' => count($this->items) + 1,
            'name' => $name,
            'item' => $url,
        ];

        $this->data['itemListElement'] = $this->items;

        return $this;
    }
    public function fromArray(array $data): static
    {
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                if (
                    isset($item['name']) &&
                    isset($item['url'])
                ) {
                    $this->item(
                        $item['name'],
                        $item['url']
                    );
                }
            }
        }

        return $this;
    }
}