<?php

namespace Therajatspace\Larakit\SEO\Schema;

use Therajatspace\Larakit\SEO\Support\UrlValidator;

class ProductSchema extends SchemaObject
{
    public function __construct()
    {
        $this->data['@type'] = 'Product';
    }

    public function image(string $url): static
    {
        UrlValidator::validate($url);

        $this->data['image'] = $url;

        return $this;
    }

    public function brand(string $name): static
    {
        $this->data['brand'] = [
            '@type' => 'Brand',
            'name' => $name,
        ];

        return $this;
    }

    public function sku(string $sku): static
    {
        $this->data['sku'] = $sku;

        return $this;
    }

    public function offers(array $offer): static
    {
        $this->data['offers'] = [
            '@type' => 'Offer',
            ...$offer,
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

        if (isset($data['brand'])) {
            $this->brand($data['brand']);
        }

        if (isset($data['sku'])) {
            $this->sku($data['sku']);
        }

        if (isset($data['offers'])) {
            $this->offers($data['offers']);
        }

        return $this;
    }
}