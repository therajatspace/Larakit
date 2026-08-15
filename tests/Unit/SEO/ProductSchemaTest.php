<?php

namespace Sidd2604\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Sidd2604\Larakit\SEO\Schema\ProductSchema;

class ProductSchemaTest extends TestCase
{
    public function test_product_has_correct_type(): void
    {
        $product = new ProductSchema();

        $this->assertSame(
            'Product',
            $product->toArray()['@type']
        );
    }

    public function test_product_image_is_stored(): void
    {
        $product = new ProductSchema();

        $data = $product
            ->image('https://example.com/product.jpg')
            ->toArray();

        $this->assertSame(
            'https://example.com/product.jpg',
            $data['image']
        );
    }

    public function test_brand_is_stored_as_brand_object(): void
    {
        $product = new ProductSchema();

        $data = $product
            ->brand('Apple')
            ->toArray();

        $this->assertSame(
            [
                '@type' => 'Brand',
                'name' => 'Apple',
            ],
            $data['brand']
        );
    }

    public function test_sku_is_stored(): void
    {
        $product = new ProductSchema();

        $data = $product
            ->sku('IPH17-001')
            ->toArray();

        $this->assertSame(
            'IPH17-001',
            $data['sku']
        );
    }

    public function test_offer_is_stored_as_offer_object(): void
    {
        $product = new ProductSchema();

        $data = $product
            ->offers([
                'price' => '79999',
                'priceCurrency' => 'INR',
                'availability' => 'https://schema.org/InStock',
            ])
            ->toArray();

        $this->assertSame(
            'Offer',
            $data['offers']['@type']
        );

        $this->assertSame(
            '79999',
            $data['offers']['price']
        );

        $this->assertSame(
            'INR',
            $data['offers']['priceCurrency']
        );

        $this->assertSame(
            'https://schema.org/InStock',
            $data['offers']['availability']
        );
    }

    public function test_invalid_product_image_throws_exception(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $product = new ProductSchema();

        $product->image('banana');
    }

    public function test_product_inherits_common_schema_properties(): void
    {
        $product = new ProductSchema();

        $data = $product
            ->name('iPhone 17')
            ->description('A powerful smartphone.')
            ->url('https://example.com/product')
            ->toArray();

        $this->assertSame(
            'iPhone 17',
            $data['name']
        );

        $this->assertSame(
            'A powerful smartphone.',
            $data['description']
        );

        $this->assertSame(
            'https://example.com/product',
            $data['url']
        );
    }
}