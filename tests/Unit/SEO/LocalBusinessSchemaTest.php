<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\LocalBusinessSchema;

class LocalBusinessSchemaTest extends TestCase
{
    public function test_local_business_has_correct_type(): void
    {
        $business = new LocalBusinessSchema();

        $this->assertSame(
            'LocalBusiness',
            $business->toArray()['@type']
        );
    }

    public function test_basic_properties_are_stored(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->name('Lara Cafe')
            ->description('A local coffee shop.')
            ->url('https://example.com')
            ->toArray();

        $this->assertSame(
            'Lara Cafe',
            $data['name']
        );

        $this->assertSame(
            'A local coffee shop.',
            $data['description']
        );

        $this->assertSame(
            'https://example.com',
            $data['url']
        );
    }

    public function test_image_is_stored(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->image('https://example.com/shop.jpg')
            ->toArray();

        $this->assertSame(
            'https://example.com/shop.jpg',
            $data['image']
        );
    }

    public function test_invalid_image_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new LocalBusinessSchema())
            ->image('not-a-url');
    }

    public function test_email_is_stored(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->email('hello@example.com')
            ->toArray();

        $this->assertSame(
            'hello@example.com',
            $data['email']
        );
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new LocalBusinessSchema())
            ->email('invalid-email');
    }

    public function test_telephone_is_stored(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->telephone('+91-9876543210')
            ->toArray();

        $this->assertSame(
            '+91-9876543210',
            $data['telephone']
        );
    }

    public function test_price_range_is_stored(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->priceRange('$$')
            ->toArray();

        $this->assertSame(
            '$$',
            $data['priceRange']
        );
    }

    public function test_address_creates_postal_address(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->address([
                'streetAddress' => '123 Main Street',
                'addressLocality' => 'Pune',
                'addressRegion' => 'Maharashtra',
                'postalCode' => '411001',
                'addressCountry' => 'IN',
            ])
            ->toArray();

        $this->assertSame(
            'PostalAddress',
            $data['address']['@type']
        );

        $this->assertSame(
            '123 Main Street',
            $data['address']['streetAddress']
        );

        $this->assertSame(
            'Pune',
            $data['address']['addressLocality']
        );

        $this->assertSame(
            'Maharashtra',
            $data['address']['addressRegion']
        );

        $this->assertSame(
            '411001',
            $data['address']['postalCode']
        );

        $this->assertSame(
            'IN',
            $data['address']['addressCountry']
        );
    }

    public function test_address_accepts_optional_fields(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->address([
                'streetAddress' => '123 Main Street',
                'addressLocality' => 'Pune',
                'addressRegion' => 'Maharashtra',
                'postalCode' => '411001',
                'addressCountry' => 'IN',
                'postOfficeBoxNumber' => 'PO123',
            ])
            ->toArray();

        $this->assertSame(
            'PO123',
            $data['address']['postOfficeBoxNumber']
        );
    }

    public function test_invalid_address_field_type_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new LocalBusinessSchema())
            ->address([
                'streetAddress' => 123,
            ]);
    }

    public function test_geo_creates_geo_coordinates(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->geo(18.5204, 73.8567)
            ->toArray();

        $this->assertSame(
            'GeoCoordinates',
            $data['geo']['@type']
        );

        $this->assertSame(
            18.5204,
            $data['geo']['latitude']
        );

        $this->assertSame(
            73.8567,
            $data['geo']['longitude']
        );
    }

    public function test_latitude_must_be_valid(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new LocalBusinessSchema())
            ->geo(91, 73);
    }

    public function test_longitude_must_be_valid(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new LocalBusinessSchema())
            ->geo(18, 181);
    }

    public function test_latitude_lower_boundary_is_valid(): void
    {
        $business = new LocalBusinessSchema();

        $business->geo(-90, 0);

        $this->assertSame(
            -90.0,
            $business->toArray()['geo']['latitude']
        );
    }

    public function test_latitude_upper_boundary_is_valid(): void
    {
        $business = new LocalBusinessSchema();

        $business->geo(90, 0);

        $this->assertSame(
            90.0,
            $business->toArray()['geo']['latitude']
        );
    }

    public function test_longitude_lower_boundary_is_valid(): void
    {
        $business = new LocalBusinessSchema();

        $business->geo(0, -180);

        $this->assertSame(
            -180.0,
            $business->toArray()['geo']['longitude']
        );
    }

    public function test_longitude_upper_boundary_is_valid(): void
    {
        $business = new LocalBusinessSchema();

        $business->geo(0, 180);

        $this->assertSame(
            180.0,
            $business->toArray()['geo']['longitude']
        );
    }

    public function test_opening_hours_accepts_string(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->openingHours('Mo-Fr 09:00-18:00')
            ->toArray();

        $this->assertSame(
            'Mo-Fr 09:00-18:00',
            $data['openingHours']
        );
    }

    public function test_opening_hours_accepts_array(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->openingHours([
                'Mo-Fr 09:00-18:00',
                'Sa 10:00-16:00',
            ])
            ->toArray();

        $this->assertSame(
            [
                'Mo-Fr 09:00-18:00',
                'Sa 10:00-16:00',
            ],
            $data['openingHours']
        );
    }

    public function test_same_as_accepts_single_url(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->sameAs('https://facebook.com/example')
            ->toArray();

        $this->assertSame(
            'https://facebook.com/example',
            $data['sameAs']
        );
    }

    public function test_same_as_accepts_multiple_urls(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->sameAs([
                'https://facebook.com/example',
                'https://instagram.com/example',
            ])
            ->toArray();

        $this->assertSame(
            [
                'https://facebook.com/example',
                'https://instagram.com/example',
            ],
            $data['sameAs']
        );
    }

    public function test_invalid_same_as_url_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new LocalBusinessSchema())
            ->sameAs('invalid');
    }

    public function test_invalid_url_inside_same_as_array_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new LocalBusinessSchema())
            ->sameAs([
                'https://facebook.com/example',
                'invalid',
            ]);
    }

    public function test_area_served_accepts_string(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->areaServed('Pune')
            ->toArray();

        $this->assertSame(
            'Pune',
            $data['areaServed']
        );
    }

    public function test_area_served_accepts_array(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->areaServed([
                'Pune',
                'Mumbai',
            ])
            ->toArray();

        $this->assertSame(
            [
                'Pune',
                'Mumbai',
            ],
            $data['areaServed']
        );
    }

    public function test_from_array_supports_all_properties(): void
    {
        $business = new LocalBusinessSchema();

        $data = $business
            ->fromArray([
                'name' => 'Lara Cafe',
                'description' => 'Coffee shop.',
                'url' => 'https://example.com',
                'image' => 'https://example.com/cafe.jpg',
                'telephone' => '+91-9876543210',
                'email' => 'hello@example.com',
                'priceRange' => '$$',
                'address' => [
                    'streetAddress' => '123 Main Street',
                    'addressLocality' => 'Pune',
                    'addressRegion' => 'Maharashtra',
                    'postalCode' => '411001',
                    'addressCountry' => 'IN',
                ],
                'geo' => [
                    'latitude' => 18.5204,
                    'longitude' => 73.8567,
                ],
                'openingHours' => [
                    'Mo-Fr 09:00-18:00',
                ],
                'sameAs' => [
                    'https://facebook.com/example',
                    'https://instagram.com/example',
                ],
                'areaServed' => [
                    'Pune',
                    'Mumbai',
                ],
            ])
            ->toArray();

        $this->assertSame(
            'LocalBusiness',
            $data['@type']
        );

        $this->assertSame(
            'Lara Cafe',
            $data['name']
        );

        $this->assertSame(
            'Coffee shop.',
            $data['description']
        );

        $this->assertSame(
            'https://example.com',
            $data['url']
        );

        $this->assertSame(
            'https://example.com/cafe.jpg',
            $data['image']
        );

        $this->assertSame(
            '+91-9876543210',
            $data['telephone']
        );

        $this->assertSame(
            'hello@example.com',
            $data['email']
        );

        $this->assertSame(
            '$$',
            $data['priceRange']
        );

        $this->assertSame(
            'PostalAddress',
            $data['address']['@type']
        );

        $this->assertSame(
            18.5204,
            $data['geo']['latitude']
        );

        $this->assertSame(
            73.8567,
            $data['geo']['longitude']
        );

        $this->assertSame(
            [
                'Mo-Fr 09:00-18:00',
            ],
            $data['openingHours']
        );

        $this->assertSame(
            [
                'https://facebook.com/example',
                'https://instagram.com/example',
            ],
            $data['sameAs']
        );

        $this->assertSame(
            [
                'Pune',
                'Mumbai',
            ],
            $data['areaServed']
        );
    }

    public function test_from_array_validates_email(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new LocalBusinessSchema())
            ->fromArray([
                'email' => 'invalid',
            ]);
    }

    public function test_from_array_validates_image(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new LocalBusinessSchema())
            ->fromArray([
                'image' => 'invalid',
            ]);
    }

    public function test_from_array_validates_geo(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new LocalBusinessSchema())
            ->fromArray([
                'geo' => [
                    'latitude' => 18.5,
                ],
            ]);
    }

    public function test_setters_are_fluent(): void
    {
        $business = new LocalBusinessSchema();

        $this->assertSame(
            $business,
            $business
                ->name('Lara Cafe')
                ->telephone('+91-9876543210')
                ->priceRange('$$')
                ->areaServed('Pune')
        );
    }

    public function test_from_array_preserves_type(): void
    {
        $business = new LocalBusinessSchema();

        $this->assertSame(
            'LocalBusiness',
            $business
                ->fromArray([
                    'name' => 'Lara Cafe',
                ])
                ->toArray()['@type']
        );
    }
}