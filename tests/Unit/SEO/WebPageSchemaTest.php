<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\ArticleSchema;
use Therajatspace\Larakit\SEO\Schema\ProductSchema;
use Therajatspace\Larakit\SEO\Schema\WebPageSchema;
use Therajatspace\Larakit\SEO\Schema\WebSiteSchema;
use Therajatspace\Larakit\SEO\Schema\BreadcrumbSchema;

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

        $this->assertSame('About Us', $data['name']);
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

        (new WebPageSchema())->image('not-a-url');
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

        (new WebPageSchema())->datePublished('not-a-date');
    }

    public function test_invalid_modified_date_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new WebPageSchema())->dateModified('not-a-date');
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

    /*
    |--------------------------------------------------------------------------
    | isPartOf / WebSite
    |--------------------------------------------------------------------------
    */

    public function test_is_part_of_creates_reference_from_string(): void
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

    public function test_is_part_of_accepts_website_schema(): void
    {
        $website = new WebSiteSchema();

        $website
            ->id('https://example.com/#website')
            ->name('LaraKit');

        $page = new WebPageSchema();

        $data = $page
            ->isPartOf($website)
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#website',
            ],
            $data['isPartOf']
        );
    }

    public function test_is_part_of_rejects_website_without_id(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $website = new WebSiteSchema();
        $website->name('LaraKit');

        (new WebPageSchema())->isPartOf($website);
    }

    public function test_is_part_of_is_fluent_with_website_schema(): void
    {
        $website = new WebSiteSchema();

        $website
            ->id('https://example.com/#website')
            ->name('LaraKit');

        $page = new WebPageSchema();

        $this->assertSame(
            $page,
            $page->isPartOf($website)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | mainEntity
    |--------------------------------------------------------------------------
    */

    public function test_main_entity_creates_reference_from_string(): void
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

    public function test_main_entity_accepts_article_schema(): void
    {
        $article = new ArticleSchema();

        $article
            ->id('https://example.com/#article')
            ->headline('Understanding Laravel');

        $page = new WebPageSchema();

        $data = $page
            ->mainEntity($article)
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#article',
            ],
            $data['mainEntity']
        );
    }

    public function test_main_entity_accepts_product_schema(): void
    {
        $product = new ProductSchema();

        $product
            ->id('https://example.com/#product')
            ->name('LaraKit Pro');

        $page = new WebPageSchema();

        $data = $page
            ->mainEntity($product)
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#product',
            ],
            $data['mainEntity']
        );
    }

    public function test_main_entity_rejects_schema_without_id(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $article = new ArticleSchema();

        $article->headline('Understanding Laravel');

        (new WebPageSchema())->mainEntity($article);
    }

    public function test_main_entity_is_fluent_with_schema_object(): void
    {
        $article = new ArticleSchema();

        $article
            ->id('https://example.com/#article')
            ->headline('Understanding Laravel');

        $page = new WebPageSchema();

        $this->assertSame(
            $page,
            $page->mainEntity($article)
        );
    }

    public function test_main_entity_can_be_replaced(): void
    {
        $first = new ArticleSchema();

        $first
            ->id('https://example.com/#first-article')
            ->headline('First Article');

        $second = new ProductSchema();

        $second
            ->id('https://example.com/#second-product')
            ->name('Second Product');

        $page = new WebPageSchema();

        $page->mainEntity($first);
        $page->mainEntity($second);

        $this->assertSame(
            [
                '@id' => 'https://example.com/#second-product',
            ],
            $page->toArray()['mainEntity']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Primary image
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Fluent API
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | fromArray
    |--------------------------------------------------------------------------
    */

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

        $this->assertSame('WebPage', $data['@type']);
        $this->assertSame('About Us', $data['name']);
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

    public function test_from_array_accepts_website_schema_as_is_part_of(): void
    {
        $website = new WebSiteSchema();

        $website
            ->id('https://example.com/#website')
            ->name('LaraKit');

        $page = new WebPageSchema();

        $data = $page
            ->fromArray([
                'name' => 'About Us',
                'isPartOf' => $website,
            ])
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#website',
            ],
            $data['isPartOf']
        );
    }

    public function test_from_array_accepts_article_as_main_entity(): void
    {
        $article = new ArticleSchema();

        $article
            ->id('https://example.com/#article')
            ->headline('Understanding Laravel');

        $page = new WebPageSchema();

        $data = $page
            ->fromArray([
                'name' => 'Laravel Article',
                'mainEntity' => $article,
            ])
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#article',
            ],
            $data['mainEntity']
        );
    }

    public function test_from_array_accepts_product_as_main_entity(): void
    {
        $product = new ProductSchema();

        $product
            ->id('https://example.com/#product')
            ->name('LaraKit Pro');

        $page = new WebPageSchema();

        $data = $page
            ->fromArray([
                'mainEntity' => $product,
            ])
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#product',
            ],
            $data['mainEntity']
        );
    }

    public function test_from_array_rejects_main_entity_without_id(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $article = new ArticleSchema();

        $article->headline('Understanding Laravel');

        (new WebPageSchema())->fromArray([
            'mainEntity' => $article,
        ]);
    }

    public function test_from_array_validates_image(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new WebPageSchema())->fromArray([
            'image' => 'invalid',
        ]);
    }

    public function test_from_array_validates_published_date(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new WebPageSchema())->fromArray([
            'datePublished' => 'invalid',
        ]);
    }

    public function test_from_array_validates_modified_date(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        (new WebPageSchema())->fromArray([
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

    /*
    |--------------------------------------------------------------------------
    | Reference integrity
    |--------------------------------------------------------------------------
    */

    public function test_nested_references_do_not_have_context(): void
    {
        $article = new ArticleSchema();

        $article
            ->id('https://example.com/#article')
            ->headline('Understanding Laravel');

        $page = new WebPageSchema();

        $data = $page
            ->isPartOf('https://example.com/#website')
            ->mainEntity($article)
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

    /*
|--------------------------------------------------------------------------
| breadcrumb
|--------------------------------------------------------------------------
*/

    public function test_breadcrumb_creates_reference_from_string(): void
    {
        $page = new WebPageSchema();

        $data = $page
            ->breadcrumb('https://example.com/#breadcrumb')
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#breadcrumb',
            ],
            $data['breadcrumb']
        );
    }

    public function test_breadcrumb_accepts_breadcrumb_schema(): void
    {
        $breadcrumb = new BreadcrumbSchema();

        $breadcrumb
            ->id('https://example.com/#breadcrumb')
            ->item(
                'Home',
                'https://example.com'
            );

        $page = new WebPageSchema();

        $data = $page
            ->breadcrumb($breadcrumb)
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#breadcrumb',
            ],
            $data['breadcrumb']
        );
    }

    public function test_breadcrumb_rejects_schema_without_id(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $breadcrumb = new BreadcrumbSchema();

        $breadcrumb->item(
            'Home',
            'https://example.com'
        );

        $page = new WebPageSchema();

        $page->breadcrumb($breadcrumb);
    }

    public function test_breadcrumb_is_fluent(): void
    {
        $breadcrumb = new BreadcrumbSchema();

        $breadcrumb
            ->id('https://example.com/#breadcrumb')
            ->item(
                'Home',
                'https://example.com'
            );

        $page = new WebPageSchema();

        $this->assertSame(
            $page,
            $page->breadcrumb($breadcrumb)
        );
    }

    public function test_breadcrumb_can_be_replaced(): void
    {
        $first = new BreadcrumbSchema();

        $first
            ->id('https://example.com/#first-breadcrumb')
            ->item(
                'Home',
                'https://example.com'
            );

        $second = new BreadcrumbSchema();

        $second
            ->id('https://example.com/#second-breadcrumb')
            ->item(
                'Home',
                'https://example.com'
            );

        $page = new WebPageSchema();

        $page->breadcrumb($first);
        $page->breadcrumb($second);

        $this->assertSame(
            [
                '@id' => 'https://example.com/#second-breadcrumb',
            ],
            $page->toArray()['breadcrumb']
        );
    }

    public function test_from_array_accepts_breadcrumb_schema(): void
    {
        $breadcrumb = new BreadcrumbSchema();

        $breadcrumb
            ->id('https://example.com/#breadcrumb')
            ->item(
                'Home',
                'https://example.com'
            );

        $page = new WebPageSchema();

        $data = $page
            ->fromArray([
                'breadcrumb' => $breadcrumb,
            ])
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#breadcrumb',
            ],
            $data['breadcrumb']
        );
    }

    public function test_breadcrumb_reference_has_no_context(): void
    {
        $breadcrumb = new BreadcrumbSchema();

        $breadcrumb
            ->id('https://example.com/#breadcrumb')
            ->item(
                'Home',
                'https://example.com'
            );

        $page = new WebPageSchema();

        $data = $page
            ->breadcrumb($breadcrumb)
            ->toArray();

        $this->assertArrayNotHasKey(
            '@context',
            $data['breadcrumb']
        );
    }
}