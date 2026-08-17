<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\OpenGraph\OpenGraphManager;

class OpenGraphManagerTest extends TestCase
{
    public function test_title_is_rendered(): void
    {
        $og = new OpenGraphManager();

        $result = $og
            ->title('LaraKit')
            ->render();

        $this->assertStringContainsString(
            '<meta property="og:title" content="LaraKit">',
            $result
        );
    }

    public function test_invalid_type_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $og = new OpenGraphManager();

        $og->type('banana');
    }

    public function test_invalid_image_url_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'Invalid Open Graph image URL: banana'
        );

        $og = new OpenGraphManager();

        $og->image('banana');
    }

    public function test_image_metadata_is_rendered(): void
    {
        $og = new OpenGraphManager();

        $result = $og->image(
            'https://example.com/larakit.jpg',
            alt: 'LaraKit SEO',
            width: 1200,
            height: 630
        )->render();

        $this->assertStringContainsString(
            '<meta property="og:image" content="https://example.com/larakit.jpg">',
            $result
        );

        $this->assertStringContainsString(
            '<meta property="og:image:alt" content="LaraKit SEO">',
            $result
        );

        $this->assertStringContainsString(
            '<meta property="og:image:width" content="1200">',
            $result
        );

        $this->assertStringContainsString(
            '<meta property="og:image:height" content="630">',
            $result
        );
    }

    public function test_multiple_images_are_rendered(): void
    {
        $og = new OpenGraphManager();

        $result = $og
            ->image(
                'https://example.com/image-one.jpg',
                alt: 'First image'
            )
            ->image(
                'https://example.com/image-two.jpg',
                alt: 'Second image'
            )
            ->render();

        $this->assertStringContainsString(
            '<meta property="og:image" content="https://example.com/image-one.jpg">',
            $result
        );

        $this->assertStringContainsString(
            '<meta property="og:image" content="https://example.com/image-two.jpg">',
            $result
        );
    }

    public function test_invalid_image_width_throws_exception(): void
{
    $this->expectException(\InvalidArgumentException::class);

    $og = new OpenGraphManager();

    $og->image(
        'https://example.com/larakit.jpg',
        width: 0
    );
}
public function test_invalid_image_height_throws_exception(): void
{
    $this->expectException(\InvalidArgumentException::class);

    $og = new OpenGraphManager();

    $og->image(
        'https://example.com/larakit.jpg',
        height: -100
    );
}
}