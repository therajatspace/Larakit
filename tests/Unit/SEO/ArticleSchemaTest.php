<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\ArticleSchema;
use Therajatspace\Larakit\SEO\Schema\PersonSchema;
use Therajatspace\Larakit\SEO\Schema\OrganizationSchema;
use Therajatspace\Larakit\SEO\Schema\WebPageSchema;

class ArticleSchemaTest extends TestCase
{
    public function test_article_has_article_type(): void
    {
        $article = new ArticleSchema();

        $this->assertSame(
            'Article',
            $article->toArray()['@type']
        );
    }

    public function test_headline_is_stored(): void
    {
        $article = new ArticleSchema();

        $data = $article
            ->headline('My Laravel Article')
            ->toArray();

        $this->assertSame(
            'My Laravel Article',
            $data['headline']
        );
    }

    public function test_author_is_stored_as_person(): void
    {
        $article = new ArticleSchema();

        $data = $article
            ->author('Siddharth')
            ->toArray();

        $this->assertSame(
            [
                '@type' => 'Person',
                'name' => 'Siddharth',
            ],
            $data['author']
        );
    }

    public function test_article_inherits_schema_object_methods(): void
    {
        $article = new ArticleSchema();

        $data = $article
            ->name('LaraKit')
            ->description('A Laravel SEO package.')
            ->url('https://example.com')
            ->toArray();

        $this->assertSame(
            'LaraKit',
            $data['name']
        );

        $this->assertSame(
            'A Laravel SEO package.',
            $data['description']
        );

        $this->assertSame(
            'https://example.com',
            $data['url']
        );
    }

    public function test_invalid_image_url_throws_exception(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $article = new ArticleSchema();

        $article->image('banana');
    }

    public function test_invalid_published_date_throws_exception(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $article = new ArticleSchema();

        $article->datePublished('banana');
    }

    public function test_from_array_validates_article_date(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $article = new ArticleSchema();

        $article->fromArray([
            'datePublished' => 'banana',
        ]);
    }

    public function test_article_can_reference_publisher(): void
    {
        $article = new ArticleSchema();

        $data = $article
            ->publisher('https://example.com/#organization')
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $data['publisher']
        );
    }

    public function test_article_can_reference_website(): void
    {
        $article = new ArticleSchema();

        $data = $article
            ->isPartOf('https://example.com/#website')
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#website',
            ],
            $data['isPartOf']
        );
    }

    public function test_article_can_reference_web_page(): void
    {
        $page = new WebPageSchema();

        $page->id('https://example.com/test/#webpage');

        $article = new ArticleSchema();

        $data = $article
            ->isPartOf($page)
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/test/#webpage',
            ],
            $data['isPartOf']
        );
    }

    public function test_is_part_of_rejects_schema_without_id(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $page = new WebPageSchema();

        $article = new ArticleSchema();

        $article->isPartOf($page);
    }

    public function test_is_part_of_is_fluent_with_schema_object(): void
    {
        $page = new WebPageSchema();

        $page->id('https://example.com/test/#webpage');

        $article = new ArticleSchema();

        $this->assertSame(
            $article,
            $article->isPartOf($page)
        );
    }

    public function test_is_part_of_can_be_replaced(): void
    {
        $firstPage = new WebPageSchema();

        $firstPage->id(
            'https://example.com/test/#first-page'
        );

        $secondPage = new WebPageSchema();

        $secondPage->id(
            'https://example.com/test/#second-page'
        );

        $article = new ArticleSchema();

        $article->isPartOf($firstPage);
        $article->isPartOf($secondPage);

        $this->assertSame(
            [
                '@id' => 'https://example.com/test/#second-page',
            ],
            $article->toArray()['isPartOf']
        );
    }


    public function test_from_array_accepts_web_page_as_is_part_of(): void
    {
        $page = new WebPageSchema();

        $page->id(
            'https://example.com/test/#webpage'
        );

        $article = new ArticleSchema();

        $data = $article
            ->fromArray([
                'headline' => 'Understanding Laravel',
                'isPartOf' => $page,
            ])
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/test/#webpage',
            ],
            $data['isPartOf']
        );
    }

    public function test_from_array_still_accepts_string_is_part_of(): void
    {
        $article = new ArticleSchema();

        $data = $article
            ->fromArray([
                'isPartOf' => 'https://example.com/#website',
            ])
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#website',
            ],
            $data['isPartOf']
        );
    }

    public function test_is_part_of_reference_has_no_context(): void
    {
        $page = new WebPageSchema();

        $page->id(
            'https://example.com/test/#webpage'
        );

        $article = new ArticleSchema();

        $data = $article
            ->isPartOf($page)
            ->toArray();

        $this->assertArrayNotHasKey(
            '@context',
            $data['isPartOf']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Author / Person
    |--------------------------------------------------------------------------
    */

    public function test_author_accepts_person_name(): void
    {
        $article = new ArticleSchema();

        $data = $article
            ->author('Siddharth Sharma')
            ->toArray();

        $this->assertSame(
            [
                '@type' => 'Person',
                'name' => 'Siddharth Sharma',
            ],
            $data['author']
        );
    }

    public function test_author_accepts_person_schema(): void
    {
        $person = new PersonSchema();

        $person
            ->id('https://example.com/#person')
            ->name('Siddharth Sharma');

        $article = new ArticleSchema();

        $data = $article
            ->author($person)
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#person',
            ],
            $data['author']
        );
    }

    public function test_author_rejects_person_without_id(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $person = new PersonSchema();

        $person->name('Siddharth Sharma');

        $article = new ArticleSchema();

        $article->author($person);
    }

    public function test_author_is_fluent_with_person_schema(): void
    {
        $person = new PersonSchema();

        $person
            ->id('https://example.com/#person')
            ->name('Siddharth Sharma');

        $article = new ArticleSchema();

        $this->assertSame(
            $article,
            $article->author($person)
        );
    }

    public function test_from_array_accepts_person_schema_as_author(): void
    {
        $person = new PersonSchema();

        $person
            ->id('https://example.com/#person')
            ->name('Siddharth Sharma');

        $article = new ArticleSchema();

        $data = $article
            ->fromArray([
                'headline' => 'Understanding Laravel',
                'author' => $person,
            ])
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#person',
            ],
            $data['author']
        );
    }

    public function test_from_array_still_accepts_string_author(): void
    {
        $article = new ArticleSchema();

        $data = $article
            ->fromArray([
                'author' => 'Siddharth Sharma',
            ])
            ->toArray();

        $this->assertSame(
            [
                '@type' => 'Person',
                'name' => 'Siddharth Sharma',
            ],
            $data['author']
        );
    }

    public function test_author_can_be_replaced(): void
    {
        $first = new PersonSchema();

        $first
            ->id('https://example.com/#first-person')
            ->name('First Author');

        $second = new PersonSchema();

        $second
            ->id('https://example.com/#second-person')
            ->name('Second Author');

        $article = new ArticleSchema();

        $article->author($first);
        $article->author($second);

        $this->assertSame(
            [
                '@id' => 'https://example.com/#second-person',
            ],
            $article->toArray()['author']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Publisher / Organization
    |--------------------------------------------------------------------------
    */

    public function test_publisher_accepts_organization_schema(): void
    {
        $organization = new OrganizationSchema();

        $organization
            ->id('https://example.com/#organization')
            ->name('LaraKit');

        $article = new ArticleSchema();

        $data = $article
            ->publisher($organization)
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $data['publisher']
        );
    }

    public function test_publisher_rejects_organization_without_id(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $organization = new OrganizationSchema();

        $organization->name('LaraKit');

        $article = new ArticleSchema();

        $article->publisher($organization);
    }

    public function test_publisher_is_fluent(): void
    {
        $organization = new OrganizationSchema();

        $organization
            ->id('https://example.com/#organization')
            ->name('LaraKit');

        $article = new ArticleSchema();

        $this->assertSame(
            $article,
            $article->publisher($organization)
        );
    }

    public function test_from_array_accepts_organization_schema_as_publisher(): void
    {
        $organization = new OrganizationSchema();

        $organization
            ->id('https://example.com/#organization')
            ->name('LaraKit');

        $article = new ArticleSchema();

        $data = $article
            ->fromArray([
                'headline' => 'Understanding Laravel',
                'publisher' => $organization,
            ])
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $data['publisher']
        );
    }

    public function test_from_array_still_accepts_string_publisher(): void
    {
        $article = new ArticleSchema();

        $data = $article
            ->fromArray([
                'publisher' => 'https://example.com/#organization',
            ])
            ->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $data['publisher']
        );
    }

    public function test_publisher_can_be_replaced(): void
    {
        $first = new OrganizationSchema();

        $first
            ->id('https://example.com/#first')
            ->name('First Organization');

        $second = new OrganizationSchema();

        $second
            ->id('https://example.com/#second')
            ->name('Second Organization');

        $article = new ArticleSchema();

        $article->publisher($first);
        $article->publisher($second);

        $this->assertSame(
            [
                '@id' => 'https://example.com/#second',
            ],
            $article->toArray()['publisher']
        );
    }
}