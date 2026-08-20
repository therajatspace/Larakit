<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\WebPageSchema;

class WebPageSchemaTest extends TestCase
{
    public function test_web_page_has_correct_type(): void
    {
        $page = new WebPageSchema();

        $this->assertSame(
            'WebPage',
            $page->toArray()['@type']
        );
    }

    public function test_web_page_supports_basic_properties(): void
    {
        $page = new WebPageSchema();

        $data = $page
            ->name('About Us')
            ->description('Learn more about our company.')
            ->url('https://example.com/about')
            ->toArray();

        $this->assertSame(
            'About Us',
            $data['name']
        );

        $this->assertSame(
            'Learn more about our company.',
            $data['description']
        );

        $this->assertSame(
            'https://example.com/about',
            $data['url']
        );
    }

    public function test_image_is_stored(): void
    {
        $page = new WebPageSchema();

        $data = $page
            ->image('https://example.com/images/about.jpg')
            ->toArray();

        $this->assertSame(
            'https://example.com/images/about.jpg',
            $data['image']
        );
    }

    public function test_invalid_image_url_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $page = new WebPageSchema();

        $page->image('not-a-url');
    }

    public function test_date_published_is_stored(): void
    {
        $page = new WebPageSchema();

        $data = $page
            ->datePublished('2026-08-20')
            ->toArray();

        $this->assertSame(
            '2026-08-20',
            $data['datePublished']
        );
    }

    public function test_date_modified_is_stored(): void
    {
        $page = new WebPageSchema();

        $data = $page
            ->dateModified('2026-08-20')
            ->toArray();

        $this->assertSame(
            '2026-08-20',
            $data['dateModified']
        );
    }

    public function test_invalid_published_date_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $page = new WebPageSchema();

        $page->datePublished('not-a-date');
    }

    public function test_invalid_modified_date_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $page = new WebPageSchema();

        $page->dateModified('not-a-date');
    }

    public function test_language_is_stored(): void
    {
        $page = new WebPageSchema();

        $data = $page
            ->inLanguage('en-US')
            ->toArray();

        $this->assertSame(
            'en-US',
            $data['inLanguage']
        );
    }

    public function test_is_part_of_creates_reference(): void
    {
        $page = new WebPageSchema();

        $data = $page
            ->isPartOf('https://example.com/#website')
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#website',
            ],
            $data['isPartOf']
        );
    }

    public function test_main_entity_creates_reference(): void
    {
        $page = new WebPageSchema();

        $data = $page
            ->mainEntity('https://example.com/#article')
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#article',
            ],
            $data['mainEntity']
        );
    }

    public function test_primary_image_creates_reference(): void
    {
        $page = new WebPageSchema();

        $data = $page
            ->primaryImageOfPage(
                'https://example.com/#image'
            )
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#image',
            ],
            $data['primaryImageOfPage']
        );
    }

    public function test_setters_are_fluent(): void
    {
        $page = new WebPageSchema();

        $this->assertSame(
            $page,
            $page
                ->name('About')
                ->description('About page')
                ->inLanguage('en')
                ->datePublished('2026-08-20')
        );
    }

    public function test_from_array_supports_all_properties(): void
    {
        $page = new WebPageSchema();

        $data = $page
            ->fromArray([
                'name' => 'About Us',
                'description' => 'About our company.',
                'url' => 'https://example.com/about',
                'image' => 'https://example.com/about.jpg',
                'datePublished' => '2026-08-01',
                'dateModified' => '2026-08-20',
                'inLanguage' => 'en-US',
                'isPartOf' => 'https://example.com/#website',
                'mainEntity' => 'https://example.com/#organization',
                'primaryImageOfPage' => 'https://example.com/#image',
            ])
            ->toArray();

        $this->assertSame(
            'WebPage',
            $data['@type']
        );

        $this->assertSame(
            'About Us',
            $data['name']
        );

        $this->assertSame(
            'About our company.',
            $data['description']
        );

        $this->assertSame(
            'https://example.com/about',
            $data['url']
        );

        $this->assertSame(
            'https://example.com/about.jpg',
            $data['image']
        );

        $this->assertSame(
            '2026-08-01',
            $data['datePublished']
        );

        $this->assertSame(
            '2026-08-20',
            $data['dateModified']
        );

        $this->assertSame(
            'en-US',
            $data['inLanguage']
        );

        $this->assertSame(
            [
                '@id' => 'https://example.com/#website',
            ],
            $data['isPartOf']
        );

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $data['mainEntity']
        );

        $this->assertSame(
            [
                '@id' => 'https://example.com/#image',
            ],
            $data['primaryImageOfPage']
        );
    }

    public function test_from_array_validates_image(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $page = new WebPageSchema();

        $page->fromArray([
            'image' => 'invalid',
        ]);
    }

    public function test_from_array_validates_published_date(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $page = new WebPageSchema();

        $page->fromArray([
            'datePublished' => 'invalid',
        ]);
    }

    public function test_from_array_validates_modified_date(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $page = new WebPageSchema();

        $page->fromArray([
            'dateModified' => 'invalid',
        ]);
    }

    public function test_from_array_preserves_web_page_type(): void
    {
        $page = new WebPageSchema();

        $data = $page
            ->fromArray([
                'name' => 'About Us',
            ])
            ->toArray();

        $this->assertSame(
            'WebPage',
            $data['@type']
        );
    }

    public function test_nested_references_do_not_have_context(): void
    {
        $page = new WebPageSchema();

        $data = $page
            ->isPartOf('https://example.com/#website')
            ->mainEntity('https://example.com/#article')
            ->primaryImageOfPage(
                'https://example.com/#image'
            )
            ->toArray();

        $this->assertArrayNotHasKey(
            '@context',
            $data['isPartOf']
        );

        $this->assertArrayNotHasKey(
            '@context',
            $data['mainEntity']
        );

        $this->assertArrayNotHasKey(
            '@context',
            $data['primaryImageOfPage']
        );
    }
}