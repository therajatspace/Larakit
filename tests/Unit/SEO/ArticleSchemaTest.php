<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\ArticleSchema;

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

        $this->assertSame('LaraKit', $data['name']);
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
}