<?php

namespace Sidd2604\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Sidd2604\Larakit\SEO\SeoManager;
use Sidd2604\Larakit\SEO\OpenGraph\OpenGraphManager;
use Sidd2604\Larakit\SEO\Twitter\TwitterCardManager;

class SeoManagerTest extends TestCase
{
    protected OpenGraphManager $openGraph;
    protected TwitterCardManager $twitter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->openGraph = $this->createMock(
            OpenGraphManager::class
        );

        $this->twitter = $this->createStub(
            TwitterCardManager::class
        );

        $this->twitter
            ->method('render')
            ->willReturn('');
    }

    public function test_title_is_rendered(): void
    {
        $this->openGraph
            ->expects($this->once())
            ->method('inherit')
            ->with(
                'LaraKit',
                null,
                null
            );

        $this->openGraph
            ->expects($this->once())
            ->method('render')
            ->willReturn('');

        $seo = new SeoManager(
            $this->openGraph,
            $this->twitter
        );

        $result = $seo
            ->title('LaraKit')
            ->render();

        $this->assertStringContainsString(
            '<title>LaraKit</title>',
            $result
        );
    }
    public function test_description_is_inherited_to_open_graph(): void
    {
        $this->openGraph
            ->expects($this->once())
            ->method('inherit')
            ->with(
                null,
                'LaraKit description',
                null
            );

        $this->openGraph
            ->expects($this->once())
            ->method('render')
            ->willReturn('');

        $seo = new SeoManager($this->openGraph, $this->twitter);

        $seo
            ->description('LaraKit description')
            ->render();
    }
    public function test_canonical_is_inherited_to_open_graph(): void
    {
        $this->openGraph
            ->expects($this->once())
            ->method('inherit')
            ->with(
                null,
                null,
                'https://example.com/larakit'
            );

        $this->openGraph
            ->expects($this->once())
            ->method('render')
            ->willReturn('');

        $seo = new SeoManager($this->openGraph, $this->twitter);

        $seo
            ->canonical('https://example.com/larakit')
            ->render();
    }
    public function test_basic_seo_values_are_inherited_to_open_graph(): void
    {
        $this->openGraph
            ->expects($this->once())
            ->method('inherit')
            ->with(
                'LaraKit',
                'A Laravel toolkit',
                'https://example.com/larakit'
            );

        $this->openGraph
            ->expects($this->once())
            ->method('render')
            ->willReturn('');

        $seo = new SeoManager($this->openGraph, $this->twitter);

        $seo
            ->title('LaraKit')
            ->description('A Laravel toolkit')
            ->canonical('https://example.com/larakit')
            ->render();
    }
    public function test_explicit_open_graph_title_overrides_basic_title(): void
    {
        $this->openGraph
            ->expects($this->once())
            ->method('title')
            ->with('Social Title');

        $this->openGraph
            ->expects($this->once())
            ->method('inherit')
            ->with(
                'Normal Title',
                null,
                null
            );

        $this->openGraph
            ->expects($this->once())
            ->method('render')
            ->willReturn('');

        $seo = new SeoManager($this->openGraph, $this->twitter);

        $seo->title('Normal Title');

        $seo->openGraph()
            ->title('Social Title');

        $seo->render();
    }
    public function test_basic_seo_values_are_inherited_to_twitter(): void
    {
        $this->openGraph
            ->expects($this->once())
            ->method('inherit')
            ->with(
                'LaraKit',
                'A Laravel SEO package.',
                null
            );

        $this->openGraph
            ->expects($this->once())
            ->method('render')
            ->willReturn('');

        $this->twitter = $this->createMock(
            TwitterCardManager::class
        );

        $this->twitter
            ->expects($this->once())
            ->method('inherit')
            ->with(
                'LaraKit',
                'A Laravel SEO package.'
            );

        $this->twitter
            ->expects($this->once())
            ->method('render')
            ->willReturn('');

        $seo = new SeoManager(
            $this->openGraph,
            $this->twitter
        );

        $seo
            ->title('LaraKit')
            ->description('A Laravel SEO package.')
            ->render();
    }
}