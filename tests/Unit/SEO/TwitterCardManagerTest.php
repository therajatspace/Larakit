<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Twitter\TwitterCardManager;

class TwitterCardManagerTest extends TestCase
{
    public function test_card_is_rendered(): void
    {
        $twitter = new TwitterCardManager();

        $result = $twitter
            ->card('summary_large_image')
            ->render();

        $this->assertStringContainsString(
            '<meta name="twitter:card" content="summary_large_image">',
            $result
        );
    }

    public function test_title_is_rendered(): void
    {
        $twitter = new TwitterCardManager();

        $result = $twitter
            ->title('LaraKit')
            ->render();

        $this->assertStringContainsString(
            '<meta name="twitter:title" content="LaraKit">',
            $result
        );
    }

    public function test_invalid_card_type_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'Invalid Twitter card type: banana'
        );

        $twitter = new TwitterCardManager();

        $twitter->card('banana');
    }
    public function test_description_is_rendered(): void
    {
        $twitter = new TwitterCardManager();

        $result = $twitter
            ->description('LaraKit SEO package.')
            ->render();

        $this->assertStringContainsString(
            '<meta name="twitter:description" content="LaraKit SEO package.">',
            $result
        );
    }
    public function test_title_can_be_inherited(): void
    {
        $twitter = new TwitterCardManager();

        $result = $twitter
            ->inherit('LaraKit')
            ->render();

        $this->assertStringContainsString(
            '<meta name="twitter:title" content="LaraKit">',
            $result
        );
    }
    public function test_description_can_be_inherited(): void
    {
        $twitter = new TwitterCardManager();

        $result = $twitter
            ->inherit(null, 'LaraKit SEO package.')
            ->render();

        $this->assertStringContainsString(
            '<meta name="twitter:description" content="LaraKit SEO package.">',
            $result
        );
    }
    public function test_explicit_title_is_not_overwritten_by_inheritance(): void
    {
        $twitter = new TwitterCardManager();

        $result = $twitter
            ->title('Twitter Title')
            ->inherit('Basic SEO Title')
            ->render();

        $this->assertStringContainsString(
            '<meta name="twitter:title" content="Twitter Title">',
            $result
        );

        $this->assertStringNotContainsString(
            'Basic SEO Title',
            $result
        );
    }
    public function test_explicit_description_is_not_overwritten_by_inheritance(): void
    {
        $twitter = new TwitterCardManager();

        $result = $twitter
            ->description('Twitter Description')
            ->inherit(null, 'Basic SEO Description')
            ->render();

        $this->assertStringContainsString(
            '<meta name="twitter:description" content="Twitter Description">',
            $result
        );

        $this->assertStringNotContainsString(
            'Basic SEO Description',
            $result
        );
    }
    public function test_image_is_rendered(): void
    {
        $twitter = new TwitterCardManager();

        $result = $twitter
            ->image(
                'https://example.com/social.jpg',
                'LaraKit social preview'
            )
            ->render();

        $this->assertStringContainsString(
            '<meta name="twitter:image" content="https://example.com/social.jpg">',
            $result
        );

        $this->assertStringContainsString(
            '<meta name="twitter:image:alt" content="LaraKit social preview">',
            $result
        );
    }
    public function test_invalid_image_url_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'Invalid Twitter image URL: banana'
        );

        $twitter = new TwitterCardManager();

        $twitter->image('banana');
    }
    public function test_image_can_be_inherited_from_open_graph(): void
    {
        $twitter = new TwitterCardManager();

        $twitter->inheritImage([
            'url' => 'https://example.com/social.jpg',
            'alt' => 'LaraKit social image',
        ]);

        $result = $twitter->render();

        $this->assertStringContainsString(
            '<meta name="twitter:image" content="https://example.com/social.jpg">',
            $result
        );

        $this->assertStringContainsString(
            '<meta name="twitter:image:alt" content="LaraKit social image">',
            $result
        );
    }
    public function test_explicit_twitter_image_is_not_overwritten(): void
    {
        $twitter = new TwitterCardManager();

        $result = $twitter
            ->image(
                'https://example.com/twitter.jpg',
                'Twitter image'
            )
            ->inheritImage([
                'url' => 'https://example.com/og.jpg',
                'alt' => 'OG image',
            ])
            ->render();

        $this->assertStringContainsString(
            '<meta name="twitter:image" content="https://example.com/twitter.jpg">',
            $result
        );

        $this->assertStringNotContainsString(
            'og.jpg',
            $result
        );
    }
    public function test_site_is_rendered(): void
    {
        $twitter = new TwitterCardManager();

        $result = $twitter
            ->site('@LaraKit')
            ->render();

        $this->assertStringContainsString(
            '<meta name="twitter:site" content="@LaraKit">',
            $result
        );
    }
    public function test_creator_is_rendered(): void
    {
        $twitter = new TwitterCardManager();

        $result = $twitter
            ->creator('@Sidd2604')
            ->render();

        $this->assertStringContainsString(
            '<meta name="twitter:creator" content="@Sidd2604">',
            $result
        );
    }
}