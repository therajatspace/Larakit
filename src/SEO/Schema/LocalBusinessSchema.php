<?php

namespace Therajatspace\Larakit\SEO\Schema;

use Therajatspace\Larakit\SEO\Support\UrlValidator;

class LocalBusinessSchema extends SchemaObject
{
    public function __construct()
    {
        $this->data['@type'] = 'LocalBusiness';
    }

    public function image(string $url): static
    {
        UrlValidator::validate($url);

        $this->data['image'] = $url;

        return $this;
    }

    public function telephone(string $telephone): static
    {
        $this->data['telephone'] = $telephone;

        return $this;
    }

    public function email(string $email): static
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                "Invalid email address: {$email}"
            );
        }

        $this->data['email'] = $email;

        return $this;
    }

    public function priceRange(string $priceRange): static
    {
        $this->data['priceRange'] = $priceRange;

        return $this;
    }

    public function address(array $address): static
    {
        $required = [
            'streetAddress',
            'addressLocality',
            'addressRegion',
            'postalCode',
            'addressCountry',
        ];

        foreach ($required as $field) {
            if (
                array_key_exists($field, $address)
                && !is_string($address[$field])
            ) {
                throw new \InvalidArgumentException(
                    "Address field [{$field}] must be a string."
                );
            }
        }

        $this->data['address'] = array_merge(
            ['@type' => 'PostalAddress'],
            $address
        );

        return $this;
    }

    public function geo(
        float $latitude,
        float $longitude
    ): static {
        if ($latitude < -90 || $latitude > 90) {
            throw new \InvalidArgumentException(
                'Latitude must be between -90 and 90.'
            );
        }

        if ($longitude < -180 || $longitude > 180) {
            throw new \InvalidArgumentException(
                'Longitude must be between -180 and 180.'
            );
        }

        $this->data['geo'] = [
            '@type' => 'GeoCoordinates',
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        return $this;
    }

    public function openingHours(
        string|array $openingHours
    ): static {
        $this->data['openingHours'] = $openingHours;

        return $this;
    }

    public function sameAs(string|array $urls): static
    {
        $urls = is_array($urls)
            ? $urls
            : [$urls];

        foreach ($urls as $url) {
            UrlValidator::validate($url);
        }

        $this->data['sameAs'] = count($urls) === 1
            ? $urls[0]
            : $urls;

        return $this;
    }

    public function areaServed(string|array $areas): static
    {
        $this->data['areaServed'] = $areas;

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

        if (isset($data['telephone'])) {
            $this->telephone($data['telephone']);
        }

        if (isset($data['email'])) {
            $this->email($data['email']);
        }

        if (isset($data['priceRange'])) {
            $this->priceRange($data['priceRange']);
        }

        if (isset($data['address'])) {
            $this->address($data['address']);
        }

        if (isset($data['geo'])) {
            if (
                !isset($data['geo']['latitude'])
                || !isset($data['geo']['longitude'])
            ) {
                throw new \InvalidArgumentException(
                    'Geo must contain latitude and longitude.'
                );
            }

            $this->geo(
                (float) $data['geo']['latitude'],
                (float) $data['geo']['longitude']
            );
        }

        if (isset($data['openingHours'])) {
            $this->openingHours(
                $data['openingHours']
            );
        }

        if (isset($data['sameAs'])) {
            $this->sameAs(
                $data['sameAs']
            );
        }

        if (isset($data['areaServed'])) {
            $this->areaServed(
                $data['areaServed']
            );
        }

        return $this;
    }
}