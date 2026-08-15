<?php

namespace Sidd2604\Larakit\SEO\Schema;

use Sidd2604\Larakit\SEO\Support\UrlValidator;

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
}