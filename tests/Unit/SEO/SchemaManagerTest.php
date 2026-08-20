<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\SchemaManager;
use Therajatspace\Larakit\SEO\Schema\SchemaObject;
use Therajatspace\Larakit\SEO\Schema\SchemaContext;
use Therajatspace\Larakit\SEO\Schema\SchemaRelationshipResolver;
use Therajatspace\Larakit\SEO\Schema\PersonSchema;
use Therajatspace\Larakit\SEO\Schema\FAQPageSchema;
use Therajatspace\Larakit\SEO\Schema\WebPageSchema;
use Therajatspace\Larakit\SEO\Schema\LocalBusinessSchema;
use Therajatspace\Larakit\SEO\Schema\ArticleSchema;
use Therajatspace\Larakit\SEO\Schema\ProductSchema;
use Therajatspace\Larakit\SEO\Schema\OrganizationSchema;
use Therajatspace\Larakit\SEO\Schema\WebSiteSchema;
use Therajatspace\Larakit\SEO\Schema\BreadcrumbSchema;

class SchemaManagerTest extends TestCase
{
    public function test_manager_can_create_schema_object(): void
    {
        $manager = $this->createSchemaManager();

        $schema = $manager->create();

        $this->assertInstanceOf(
            SchemaObject::class,
            $schema
        );
    }

    public function test_schema_object_contains_context(): void
    {
        $manager = $this->createSchemaManager();

        $schema = $manager->create();

        $data = $schema->toArray();

        $this->assertSame(
            'https://schema.org',
            $data['@context']
        );
    }

    public function test_schema_object_properties_are_stored(): void
    {
        $manager = $this->createSchemaManager();

        $schema = $manager->create();

        $data = $schema
            ->type('Article')
            ->name('LaraKit')
            ->description('A Laravel SEO package.')
            ->url('https://example.com')
            ->toArray();

        $this->assertSame(
            'Article',
            $data['@type']
        );

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

    public function test_custom_property_is_stored(): void
    {
        $manager = $this->createSchemaManager();

        $schema = $manager->create();

        $data = $schema
            ->type('Article')
            ->property('author', [
                '@type' => 'Person',
                'name' => 'Siddharth',
            ])
            ->toArray();

        $this->assertSame(
            [
                '@type' => 'Person',
                'name' => 'Siddharth',
            ],
            $data['author']
        );
    }

    public function test_schema_is_rendered_as_json_ld(): void
    {
        $manager = $this->createSchemaManager();

        $manager
            ->create()
            ->type('Article')
            ->name('LaraKit');

        $result = $manager->render();

        $this->assertStringContainsString(
            '<script type="application/ld+json">',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"Article"',
            $result
        );

        $this->assertStringContainsString(
            '"name":"LaraKit"',
            $result
        );
    }

    public function test_multiple_schema_objects_are_rendered(): void
    {
        $manager = $this->createSchemaManager();

        $manager
            ->create()
            ->type('Article')
            ->name('LaraKit Article');

        $manager
            ->create()
            ->type('Organization')
            ->name('LaraKit');

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '<script type="application/ld+json">'
            )
        );

        $this->assertStringContainsString(
            '"@graph"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"Article"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"Organization"',
            $result
        );
    }

    public function test_schema_objects_are_independent(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager
            ->create()
            ->type('Article')
            ->name('My Article');

        $organization = $manager
            ->create()
            ->type('Organization')
            ->name('LaraKit');

        $articleData = $article->toArray();
        $organizationData = $organization->toArray();

        $this->assertSame(
            'Article',
            $articleData['@type']
        );

        $this->assertSame(
            'Organization',
            $organizationData['@type']
        );

        $this->assertNotSame(
            $articleData['@type'],
            $organizationData['@type']
        );
    }

    public function test_nested_schema_property_is_valid_json(): void
    {
        $manager = $this->createSchemaManager();

        $manager
            ->create()
            ->type('Article')
            ->property('author', [
                '@type' => 'Person',
                'name' => 'Siddharth',
            ]);

        $result = $manager->render();

        preg_match(
            '/<script type="application\/ld\+json">(.*?)<\/script>/s',
            $result,
            $matches
        );

        $this->assertNotEmpty($matches);

        $data = json_decode(
            $matches[1],
            true
        );

        $this->assertSame(
            JSON_ERROR_NONE,
            json_last_error()
        );

        $article = $data['@graph'][0];

        $this->assertSame(
            'Person',
            $article['author']['@type']
        );

        $this->assertSame(
            'Siddharth',
            $article['author']['name']
        );
    }

    public function test_manager_can_create_schema_using_class_name(): void
    {
        $manager = $this->createSchemaManager();

        $schema = $manager->create(
            \Therajatspace\Larakit\SEO\Schema\ProductSchema::class
        );

        $this->assertInstanceOf(
            \Therajatspace\Larakit\SEO\Schema\ProductSchema::class,
            $schema
        );

        $this->assertSame(
            1,
            $manager->count()
        );
    }

    public function test_factory_rejects_non_schema_class(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $manager = $this->createSchemaManager();

        $manager->create(\stdClass::class);
    }

    public function test_factory_rejects_non_existing_class(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $manager = $this->createSchemaManager();

        $manager->create(
            'Therajatspace\Larakit\SEO\Schema\DoesNotExist'
        );
    }

    public function test_manager_can_generate_global_schema_id(): void
    {
        $manager = $this->createSchemaManager();

        $this->assertSame(
            'https://example.com/#organization',
            $manager->id('organization')
        );
    }

    public function test_manager_can_generate_current_page_schema_id(): void
    {
        $manager = $this->createSchemaManager();

        $this->assertSame(
            'https://example.com/test/#article',
            $manager->pageId('article')
        );
    }

    public function test_organization_gets_automatic_id(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization();

        $this->assertSame(
            'https://example.com/#organization',
            $organization->toArray()['@id']
        );
    }

    public function test_website_gets_automatic_id(): void
    {
        $manager = $this->createSchemaManager();

        $website = $manager->website();

        $this->assertSame(
            'https://example.com/#website',
            $website->toArray()['@id']
        );
    }

    public function test_article_gets_automatic_page_id(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article();

        $this->assertSame(
            'https://example.com/test/#article',
            $article->toArray()['@id']
        );
    }

    public function test_product_gets_automatic_page_id(): void
    {
        $manager = $this->createSchemaManager();

        $product = $manager->product();

        $this->assertSame(
            'https://example.com/test/#product',
            $product->toArray()['@id']
        );
    }

    public function test_breadcrumb_gets_automatic_page_id(): void
    {
        $manager = $this->createSchemaManager();

        $breadcrumb = $manager->breadcrumbs();

        $this->assertSame(
            'https://example.com/test/#breadcrumb',
            $breadcrumb->toArray()['@id']
        );
    }

    protected function createSchemaManager(): SchemaManager
    {
        return new SchemaManager(
            new SchemaContext(
                'https://example.com',
                'https://example.com/test'
            ),
            new SchemaRelationshipResolver()
        );
    }

    public function test_schemas_can_be_connected(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $manager->connect(
            $website,
            'publisher',
            $organization
        );

        $data = $website->toArray();

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $data['publisher']
        );
    }

    public function test_website_is_automatically_connected_to_organization(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $manager->render();

        $this->assertSame(
            [
                '@id' => $organization->toArray()['@id'],
            ],
            $website->toArray()['publisher']
        );
    }

    public function test_article_is_automatically_connected_to_organization(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $article = $manager->article([
            'headline' => 'Laravel SEO',
        ]);

        $manager->render();

        $this->assertSame(
            [
                '@id' => $organization->toArray()['@id'],
            ],
            $article->toArray()['publisher']
        );
    }

    public function test_article_is_automatically_connected_to_website(): void
    {
        $manager = $this->createSchemaManager();

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $article = $manager->article([
            'headline' => 'Laravel SEO',
        ]);

        $manager->render();

        $this->assertSame(
            [
                '@id' => $website->toArray()['@id'],
            ],
            $article->toArray()['isPartOf']
        );
    }

    public function test_article_without_organization_has_no_publisher(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article([
            'headline' => 'Laravel SEO',
        ]);

        $manager->render();

        $this->assertArrayNotHasKey(
            'publisher',
            $article->toArray()
        );
    }

    public function test_article_without_website_has_no_is_part_of(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article([
            'headline' => 'Laravel SEO',
        ]);

        $manager->render();

        $this->assertArrayNotHasKey(
            'isPartOf',
            $article->toArray()
        );
    }

    public function test_schemas_without_ids_are_not_automatically_connected(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager
            ->create()
            ->type('Organization')
            ->name('LaraKit');

        $article = $manager
            ->create()
            ->type('Article')
            ->name('LaraKit Article');

        $manager->render();

        $this->assertArrayNotHasKey(
            'publisher',
            $article->toArray()
        );
    }

    public function test_person_gets_automatic_page_id(): void
    {
        $manager = $this->createSchemaManager();

        $person = $manager->person([
            'name' => 'Siddharth Sharma',
        ]);

        $this->assertSame(
            'https://example.com/test/#person',
            $person->toArray()['@id']
        );
    }
    public function test_person_is_added_to_schema_graph(): void
    {
        $manager = $this->createSchemaManager();

        $person = $manager->person([
            'name' => 'Siddharth Sharma',
        ]);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"Person"',
            $result
        );

        $this->assertStringContainsString(
            '"name":"Siddharth Sharma"',
            $result
        );

        $this->assertStringContainsString(
            '"@id":"https://example.com/test/#person"',
            $result
        );
    }
    public function test_person_can_coexist_with_other_schemas(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $person = $manager->person([
            'name' => 'Siddharth Sharma',
        ]);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"Organization"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"Person"',
            $result
        );

        $this->assertStringContainsString(
            '"name":"LaraKit"',
            $result
        );

        $this->assertStringContainsString(
            '"name":"Siddharth Sharma"',
            $result
        );
    }
    public function test_from_array_validates_image_url(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $person = new PersonSchema();

        $person->fromArray([
            'image' => 'not-a-url',
        ]);
    }
    public function test_from_array_validates_same_as_url(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $person = new PersonSchema();

        $person->fromArray([
            'sameAs' => 'not-a-url',
        ]);
    }
    public function test_faq_page_gets_automatic_page_id(): void
    {
        $manager = $this->createSchemaManager();

        $faq = $manager->faqPage();

        $this->assertSame(
            'https://example.com/test/#faq',
            $faq->toArray()['@id']
        );
    }
    public function test_manager_can_create_faq_page(): void
    {
        $manager = $this->createSchemaManager();

        $faq = $manager->faqPage([
            'name' => 'Frequently Asked Questions',
            'questions' => [
                [
                    'question' => 'What is LaraKit?',
                    'answer' => 'A Laravel SEO toolkit.',
                ],
            ],
        ]);

        $this->assertInstanceOf(
            FAQPageSchema::class,
            $faq
        );

        $data = $faq->toArray();

        $this->assertSame(
            'FAQPage',
            $data['@type']
        );

        $this->assertSame(
            'Frequently Asked Questions',
            $data['name']
        );

        $this->assertCount(
            1,
            $data['mainEntity']
        );
    }
    public function test_faq_page_is_rendered_as_valid_json_ld(): void
    {
        $manager = $this->createSchemaManager();

        $manager->faqPage([
            'name' => 'Frequently Asked Questions',
            'questions' => [
                [
                    'question' => 'What is LaraKit?',
                    'answer' => 'A Laravel SEO toolkit.',
                ],
                [
                    'question' => 'Is LaraKit open source?',
                    'answer' => 'Yes.',
                ],
            ],
        ]);

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '<script type="application/ld+json">'
            )
        );

        $this->assertStringContainsString(
            '"@type":"FAQPage"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"Question"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"Answer"',
            $result
        );

        $this->assertStringContainsString(
            '"name":"What is LaraKit?"',
            $result
        );

        $this->assertStringContainsString(
            '"text":"A Laravel SEO toolkit."',
            $result
        );
    }
    public function test_faq_page_produces_correct_json_structure(): void
    {
        $manager = $this->createSchemaManager();

        $manager->faqPage([
            'questions' => [
                [
                    'question' => 'What is LaraKit?',
                    'answer' => 'A Laravel SEO toolkit.',
                ],
            ],
        ]);

        $result = $manager->render();

        preg_match(
            '/<script type="application\/ld\+json">(.*?)<\/script>/s',
            $result,
            $matches
        );

        $this->assertNotEmpty($matches);

        $data = json_decode(
            $matches[1],
            true
        );

        $this->assertSame(
            JSON_ERROR_NONE,
            json_last_error()
        );

        $faq = $data['@graph'][0];

        $this->assertSame(
            'FAQPage',
            $faq['@type']
        );

        $this->assertSame(
            'https://example.com/test/#faq',
            $faq['@id']
        );

        $this->assertCount(
            1,
            $faq['mainEntity']
        );

        $question = $faq['mainEntity'][0];

        $this->assertSame(
            'Question',
            $question['@type']
        );

        $this->assertSame(
            'What is LaraKit?',
            $question['name']
        );

        $this->assertSame(
            'Answer',
            $question['acceptedAnswer']['@type']
        );

        $this->assertSame(
            'A Laravel SEO toolkit.',
            $question['acceptedAnswer']['text']
        );
    }
    public function test_faq_page_can_coexist_with_other_schemas(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $faq = $manager->faqPage([
            'name' => 'FAQ',
            'questions' => [
                [
                    'question' => 'What is LaraKit?',
                    'answer' => 'A Laravel SEO toolkit.',
                ],
            ],
        ]);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"Organization"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"FAQPage"',
            $result
        );

        $this->assertSame(
            'https://example.com/#organization',
            $organization->toArray()['@id']
        );

        $this->assertSame(
            'https://example.com/test/#faq',
            $faq->toArray()['@id']
        );
    }
    public function test_faq_page_has_no_unexpected_automatic_relationships(): void
    {
        $manager = $this->createSchemaManager();

        $faq = $manager->faqPage([
            'questions' => [
                [
                    'question' => 'What is LaraKit?',
                    'answer' => 'A Laravel SEO toolkit.',
                ],
            ],
        ]);

        $manager->render();

        $data = $faq->toArray();

        $this->assertArrayNotHasKey(
            'publisher',
            $data
        );

        $this->assertArrayNotHasKey(
            'isPartOf',
            $data
        );
    }
    public function test_faq_page_with_no_questions_can_be_rendered(): void
    {
        $manager = $this->createSchemaManager();

        $faq = $manager->faqPage([
            'name' => 'Frequently Asked Questions',
        ]);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"FAQPage"',
            $result
        );

        $this->assertSame(
            [],
            $faq->toArray()['mainEntity']
        );
    }

    public function test_web_page_gets_automatic_page_id(): void
    {
        $manager = $this->createSchemaManager();

        $page = $manager->webPage([
            'name' => 'About Us',
        ]);

        $this->assertSame(
            'https://example.com/test/#webpage',
            $page->toArray()['@id']
        );
    }

    public function test_manager_can_create_web_page(): void
    {
        $manager = $this->createSchemaManager();

        $page = $manager->webPage([
            'name' => 'About Us',
            'description' => 'About our company.',
            'url' => 'https://example.com/about',
        ]);

        $this->assertInstanceOf(
            WebPageSchema::class,
            $page
        );

        $this->assertSame(
            'WebPage',
            $page->toArray()['@type']
        );

        $this->assertSame(
            'About Us',
            $page->toArray()['name']
        );
    }

    public function test_web_page_is_rendered_in_graph(): void
    {
        $manager = $this->createSchemaManager();

        $manager->webPage([
            'name' => 'About Us',
            'description' => 'About our company.',
        ]);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"WebPage"',
            $result
        );

        $this->assertStringContainsString(
            '"name":"About Us"',
            $result
        );

        $this->assertStringContainsString(
            '"@id":"https://example.com/test/#webpage"',
            $result
        );
    }

    public function test_web_page_produces_valid_json_structure(): void
    {
        $manager = $this->createSchemaManager();

        $manager->webPage([
            'name' => 'About Us',
            'url' => 'https://example.com/about',
            'inLanguage' => 'en-US',
        ]);

        $result = $manager->render();

        preg_match(
            '/<script type="application\/ld\+json">(.*?)<\/script>/s',
            $result,
            $matches
        );

        $this->assertNotEmpty($matches);

        $data = json_decode(
            $matches[1],
            true
        );

        $this->assertSame(
            JSON_ERROR_NONE,
            json_last_error()
        );

        $page = null;

        foreach ($data['@graph'] as $node) {
            if (($node['@type'] ?? null) === 'WebPage') {
                $page = $node;
                break;
            }
        }

        $this->assertNotNull($page);

        $this->assertSame(
            'WebPage',
            $page['@type']
        );

        $this->assertSame(
            'https://example.com/test/#webpage',
            $page['@id']
        );

        $this->assertSame(
            'About Us',
            $page['name']
        );

        $this->assertSame(
            'https://example.com/about',
            $page['url']
        );

        $this->assertSame(
            'en-US',
            $page['inLanguage']
        );
    }


    public function test_web_page_can_coexist_with_other_schemas(): void
    {
        $manager = $this->createSchemaManager();

        $manager->organization([
            'name' => 'LaraKit',
        ]);

        $manager->webPage([
            'name' => 'About Us',
        ]);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"Organization"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"WebPage"',
            $result
        );
    }

    public function test_local_business_gets_automatic_page_id(): void
    {
        $manager = $this->createSchemaManager();

        $business = $manager->localBusiness([
            'name' => 'Lara Cafe',
        ]);

        $this->assertSame(
            'https://example.com/test/#localbusiness',
            $business->toArray()['@id']
        );
    }

    public function test_manager_can_create_local_business(): void
    {
        $manager = $this->createSchemaManager();

        $business = $manager->localBusiness([
            'name' => 'Lara Cafe',
            'telephone' => '+91-9876543210',
            'address' => [
                'streetAddress' => '123 Main Street',
                'addressLocality' => 'Pune',
                'addressRegion' => 'Maharashtra',
                'postalCode' => '411001',
                'addressCountry' => 'IN',
            ],
        ]);

        $this->assertInstanceOf(
            LocalBusinessSchema::class,
            $business
        );

        $this->assertSame(
            'LocalBusiness',
            $business->toArray()['@type']
        );

        $this->assertSame(
            'Lara Cafe',
            $business->toArray()['name']
        );
    }

    public function test_local_business_is_rendered_in_graph(): void
    {
        $manager = $this->createSchemaManager();

        $manager->localBusiness([
            'name' => 'Lara Cafe',
            'url' => 'https://example.com',
            'telephone' => '+91-9876543210',
        ]);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"LocalBusiness"',
            $result
        );

        $this->assertStringContainsString(
            '"name":"Lara Cafe"',
            $result
        );

        $this->assertStringContainsString(
            '"@id":"https://example.com/test/#localbusiness"',
            $result
        );
    }

    public function test_local_business_produces_valid_json_structure(): void
    {
        $manager = $this->createSchemaManager();

        $manager->localBusiness([
            'name' => 'Lara Cafe',
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
        ]);

        $result = $manager->render();

        preg_match(
            '/<script type="application\/ld\+json">(.*?)<\/script>/s',
            $result,
            $matches
        );

        $this->assertNotEmpty($matches);

        $data = json_decode(
            $matches[1],
            true
        );

        $this->assertSame(
            JSON_ERROR_NONE,
            json_last_error()
        );

        $business = null;

        foreach ($data['@graph'] as $node) {
            if (($node['@type'] ?? null) === 'LocalBusiness') {
                $business = $node;
                break;
            }
        }

        $this->assertNotNull($business);

        $this->assertSame(
            'LocalBusiness',
            $business['@type']
        );

        $this->assertSame(
            'https://example.com/test/#localbusiness',
            $business['@id']
        );

        $this->assertSame(
            'Lara Cafe',
            $business['name']
        );

        $this->assertSame(
            'PostalAddress',
            $business['address']['@type']
        );

        $this->assertSame(
            'Pune',
            $business['address']['addressLocality']
        );

        $this->assertSame(
            'GeoCoordinates',
            $business['geo']['@type']
        );
    }

    public function test_local_business_can_coexist_with_other_schemas(): void
    {
        $manager = $this->createSchemaManager();

        $manager->organization([
            'name' => 'LaraKit',
        ]);

        $manager->localBusiness([
            'name' => 'Lara Cafe',
        ]);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"Organization"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"LocalBusiness"',
            $result
        );
    }

    public function test_article_can_reference_person_in_schema_graph(): void
    {
        $manager = $this->createSchemaManager();

        $person = $manager->person([
            'name' => 'Siddharth Sharma',
            'jobTitle' => 'Laravel Developer',
        ]);

        $article = $manager->article([
            'headline' => 'Understanding Laravel',
        ]);

        $article->author($person);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"Person"',
            $result
        );

        $this->assertStringContainsString(
            '"name":"Siddharth Sharma"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"Article"',
            $result
        );

        $this->assertStringContainsString(
            '"author":{"@id":"https://example.com/test/#person"',
            $result
        );
    }

    public function test_article_author_uses_reference_instead_of_duplicate_person(): void
    {
        $manager = $this->createSchemaManager();

        $person = $manager->person([
            'name' => 'Siddharth Sharma',
        ]);

        $article = $manager->article([
            'headline' => 'Understanding Laravel',
        ]);

        $article->author($person);

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"Person"'
            )
        );

        $this->assertStringContainsString(
            '"author":{"@id":"https://example.com/test/#person"',
            $result
        );
    }

    public function test_multiple_articles_can_reference_same_person(): void
    {
        $manager = $this->createSchemaManager();

        $person = $manager->person([
            'name' => 'Siddharth Sharma',
        ]);

        $articleOne = $manager->article([
            'headline' => 'Laravel Basics',
        ]);

        $articleTwo = $manager->article([
            'headline' => 'Advanced Laravel',
        ]);

        $articleOne->author($person);
        $articleTwo->author($person);

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"Person"'
            )
        );

        $this->assertSame(
            2,
            substr_count(
                $result,
                '"author":{"@id":"https://example.com/test/#person"'
            )
        );
    }


    public function test_article_can_reference_organization_as_publisher(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $article = $manager->article([
            'headline' => 'Understanding Laravel',
        ]);

        $article->publisher($organization);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"Organization"',
            $result
        );

        $this->assertStringContainsString(
            '"name":"LaraKit"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"Article"',
            $result
        );

        $this->assertStringContainsString(
            '"publisher":{"@id":"https://example.com/#organization"}',
            $result
        );
    }

    public function test_article_publisher_uses_reference_instead_of_duplicate_organization(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $article = $manager->article([
            'headline' => 'Understanding Laravel',
        ]);

        $article->publisher($organization);

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"Organization"'
            )
        );

        $this->assertStringContainsString(
            '"publisher":{"@id":"https://example.com/#organization"}',
            $result
        );
    }

    public function test_multiple_articles_can_reference_same_publisher(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $articleOne = $manager->article([
            'headline' => 'Laravel Basics',
        ]);

        $articleTwo = $manager->article([
            'headline' => 'Advanced Laravel',
        ]);

        $articleTwo->id(
            'https://example.com/test/#article-two'
        );

        $articleOne->publisher($organization);
        $articleTwo->publisher($organization);

        $result = $manager->render();

        preg_match(
            '/<script type="application\/ld\+json">(.*?)<\/script>/s',
            $result,
            $matches
        );

        $this->assertNotEmpty($matches);

        $data = json_decode(
            $matches[1],
            true
        );

        $this->assertSame(
            JSON_ERROR_NONE,
            json_last_error()
        );

        $this->assertArrayHasKey(
            '@graph',
            $data
        );

        $organizations = array_values(
            array_filter(
                $data['@graph'],
                fn(array $node) =>
                ($node['@type'] ?? null) === 'Organization'
            )
        );

        $articles = array_values(
            array_filter(
                $data['@graph'],
                fn(array $node) =>
                ($node['@type'] ?? null) === 'Article'
            )
        );

        $this->assertCount(
            1,
            $organizations
        );

        $this->assertCount(
            2,
            $articles
        );

        $this->assertSame(
            'https://example.com/#organization',
            $organizations[0]['@id']
        );

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $articles[0]['publisher']
        );

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $articles[1]['publisher']
        );
    }

    public function test_article_automatic_publisher_relationship_still_works(): void
    {
        $manager = $this->createSchemaManager();

        $manager->organization([
            'name' => 'LaraKit',
        ]);

        $manager->article([
            'headline' => 'Understanding Laravel',
        ]);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"Organization"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"Article"',
            $result
        );

        $this->assertStringContainsString(
            '"publisher":{"@id":"https://example.com/#organization"}',
            $result
        );
    }

    public function test_web_page_can_reference_website(): void
    {
        $manager = $this->createSchemaManager();

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $page = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('webpage'))
            ->name('About Us');

        $page->isPartOf($website);

        $this->assertSame(
            [
                '@id' => $website->toArray()['@id'],
            ],
            $page->toArray()['isPartOf']
        );
    }

    public function test_web_page_website_relationship_is_rendered(): void
    {
        $manager = $this->createSchemaManager();

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $page = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('webpage'))
            ->name('About Us');

        $page->isPartOf($website);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"WebSite"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"WebPage"',
            $result
        );

        $this->assertStringContainsString(
            '"isPartOf":{"@id":"https://example.com/#website"}',
            $result
        );
    }

    public function test_web_page_references_existing_website_without_duplication(): void
    {
        $manager = $this->createSchemaManager();

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $page = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('webpage'))
            ->name('About Us');

        $page->isPartOf($website);

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"WebSite"'
            )
        );

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"WebPage"'
            )
        );

        $this->assertStringContainsString(
            '"isPartOf":{"@id":"https://example.com/#website"}',
            $result
        );
    }

    public function test_multiple_web_pages_can_reference_same_website(): void
    {
        $manager = $this->createSchemaManager();

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $pageOne = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('about'))
            ->name('About Us');

        $pageTwo = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('contact'))
            ->name('Contact Us');

        $pageOne->isPartOf($website);
        $pageTwo->isPartOf($website);

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"WebSite"'
            )
        );

        $this->assertSame(
            2,
            substr_count(
                $result,
                '"@type":"WebPage"'
            )
        );

        $this->assertSame(
            2,
            substr_count(
                $result,
                '"isPartOf":{"@id":"https://example.com/#website"}'
            )
        );
    }

    public function test_website_organization_relationship_still_works_with_web_page(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $page = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('webpage'))
            ->name('About Us');

        $page->isPartOf($website);

        $manager->render();

        $this->assertSame(
            [
                '@id' => $organization->toArray()['@id'],
            ],
            $website->toArray()['publisher']
        );

        $this->assertSame(
            [
                '@id' => $website->toArray()['@id'],
            ],
            $page->toArray()['isPartOf']
        );
    }

    public function test_web_page_can_reference_article_as_main_entity(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article([
            'headline' => 'Understanding Laravel',
        ]);

        $page = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('webpage'))
            ->name('Laravel Article');

        $page->mainEntity($article);

        $this->assertSame(
            [
                '@id' => $article->toArray()['@id'],
            ],
            $page->toArray()['mainEntity']
        );
    }

    public function test_web_page_can_reference_product_as_main_entity(): void
    {
        $manager = $this->createSchemaManager();

        $product = $manager->product([
            'name' => 'LaraKit Pro',
        ]);

        $page = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('product-page'))
            ->name('LaraKit Pro');

        $page->mainEntity($product);

        $this->assertSame(
            [
                '@id' => $product->toArray()['@id'],
            ],
            $page->toArray()['mainEntity']
        );
    }

    public function test_web_page_main_entity_relationship_is_rendered(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article([
            'headline' => 'Understanding Laravel',
        ]);

        $page = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('webpage'))
            ->name('Laravel Article');

        $page->mainEntity($article);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"Article"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"WebPage"',
            $result
        );

        $this->assertStringContainsString(
            '"mainEntity":{"@id":"https://example.com/test/#article"}',
            $result
        );
    }

    public function test_web_page_main_entity_uses_reference_without_duplicate_article(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article([
            'headline' => 'Understanding Laravel',
        ]);

        $page = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('webpage'))
            ->name('Laravel Article');

        $page->mainEntity($article);

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"Article"'
            )
        );

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"WebPage"'
            )
        );

        $this->assertStringContainsString(
            '"mainEntity":{"@id":"https://example.com/test/#article"}',
            $result
        );
    }

    public function test_multiple_web_pages_can_reference_same_main_entity(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article([
            'headline' => 'Understanding Laravel',
        ]);

        $pageOne = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('article-page'))
            ->name('Article Page');

        $pageTwo = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('featured-article'))
            ->name('Featured Article');

        $pageOne->mainEntity($article);
        $pageTwo->mainEntity($article);

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"Article"'
            )
        );

        $this->assertSame(
            2,
            substr_count(
                $result,
                '"@type":"WebPage"'
            )
        );

        $this->assertSame(
            2,
            substr_count(
                $result,
                '"mainEntity":{"@id":"https://example.com/test/#article"}'
            )
        );
    }

    public function test_website_can_reference_organization_as_publisher(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $website->publisher($organization);

        $this->assertSame(
            [
                '@id' => 'https://example.com/#organization',
            ],
            $website->toArray()['publisher']
        );
    }

    public function test_website_publisher_relationship_is_rendered(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $website->publisher($organization);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"Organization"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"WebSite"',
            $result
        );

        $this->assertStringContainsString(
            '"publisher":{"@id":"https://example.com/#organization"}',
            $result
        );
    }

    public function test_website_publisher_uses_reference_without_duplicate_organization(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $website = $manager->website([
            'name' => 'LaraKit',
        ]);

        $website->publisher($organization);

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"Organization"'
            )
        );

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"WebSite"'
            )
        );

        $this->assertStringContainsString(
            '"publisher":{"@id":"https://example.com/#organization"}',
            $result
        );
    }

    public function test_multiple_websites_can_reference_same_publisher(): void
    {
        $manager = $this->createSchemaManager();

        $organization = $manager->organization([
            'name' => 'LaraKit',
        ]);

        $websiteOne = $manager->website([
            'name' => 'LaraKit Main',
        ]);

        $websiteTwo = $manager->create(WebSiteSchema::class)
            ->id('https://example.com/#second-website')
            ->name('LaraKit Docs');

        $websiteOne->publisher($organization);
        $websiteTwo->publisher($organization);

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"Organization"'
            )
        );

        $this->assertSame(
            2,
            substr_count(
                $result,
                '"@type":"WebSite"'
            )
        );

        $this->assertSame(
            2,
            substr_count(
                $result,
                '"publisher":{"@id":"https://example.com/#organization"}'
            )
        );
    }

    public function test_web_page_can_reference_breadcrumb(): void
    {
        $manager = $this->createSchemaManager();

        $breadcrumb = $manager
            ->breadcrumbs()
            ->id('https://example.com/#breadcrumb')
            ->item(
                'Home',
                'https://example.com'
            )
            ->item(
                'Blog',
                'https://example.com/blog'
            );

        $page = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('blog'))
            ->name('Blog');

        $page->breadcrumb($breadcrumb);

        $this->assertSame(
            [
                '@id' => 'https://example.com/#breadcrumb',
            ],
            $page->toArray()['breadcrumb']
        );
    }

    public function test_web_page_breadcrumb_relationship_is_rendered(): void
    {
        $manager = $this->createSchemaManager();

        $breadcrumb = $manager
            ->breadcrumbs()
            ->id('https://example.com/#breadcrumb')
            ->item(
                'Home',
                'https://example.com'
            )
            ->item(
                'Blog',
                'https://example.com/blog'
            );

        $page = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('blog'))
            ->name('Blog');

        $page->breadcrumb($breadcrumb);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"BreadcrumbList"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"WebPage"',
            $result
        );

        $this->assertStringContainsString(
            '"breadcrumb":{"@id":"https://example.com/#breadcrumb"}',
            $result
        );
    }

    public function test_web_page_breadcrumb_uses_reference_without_duplicate_breadcrumb_list(): void
    {
        $manager = $this->createSchemaManager();

        $breadcrumb = $manager
            ->breadcrumbs()
            ->id('https://example.com/#breadcrumb')
            ->item(
                'Home',
                'https://example.com'
            );

        $page = $manager
            ->create(WebPageSchema::class)
            ->id($manager->pageId('blog'))
            ->name('Blog');

        $page->breadcrumb($breadcrumb);

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"BreadcrumbList"'
            )
        );

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"WebPage"'
            )
        );

        $this->assertStringContainsString(
            '"breadcrumb":{"@id":"https://example.com/#breadcrumb"}',
            $result
        );
    }

    public function test_article_can_reference_web_page_as_is_part_of(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article([
            'headline' => 'Understanding Laravel',
        ]);

        $page = $manager
            ->create(WebPageSchema::class)
            ->id('https://example.com/test/#webpage')
            ->name('Understanding Laravel');

        $article->isPartOf($page);

        $this->assertSame(
            [
                '@id' => $page->toArray()['@id'],
            ],
            $article->toArray()['isPartOf']
        );
    }

    public function test_article_web_page_relationship_is_rendered(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article([
            'headline' => 'Understanding Laravel',
        ]);

        $page = $manager
            ->create(WebPageSchema::class)
            ->id('https://example.com/test/#webpage')
            ->name('Understanding Laravel');

        $article->isPartOf($page);

        $result = $manager->render();

        $this->assertStringContainsString(
            '"@type":"Article"',
            $result
        );

        $this->assertStringContainsString(
            '"@type":"WebPage"',
            $result
        );

        $this->assertStringContainsString(
            '"isPartOf":{"@id":"https://example.com/test/#webpage"}',
            $result
        );
    }

    public function test_article_is_part_of_uses_web_page_reference_without_duplication(): void
    {
        $manager = $this->createSchemaManager();

        $article = $manager->article([
            'headline' => 'Understanding Laravel',
        ]);

        $page = $manager
            ->create(WebPageSchema::class)
            ->id('https://example.com/test/#webpage')
            ->name('Understanding Laravel');

        $article->isPartOf($page);

        $result = $manager->render();

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"Article"'
            )
        );

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"WebPage"'
            )
        );

        $this->assertStringContainsString(
            '"isPartOf":{"@id":"https://example.com/test/#webpage"}',
            $result
        );
    }

    public function test_multiple_articles_can_reference_same_web_page(): void
    {
        $manager = $this->createSchemaManager();

        $page = $manager
            ->create(WebPageSchema::class)
            ->id('https://example.com/test/#webpage')
            ->name('Laravel Articles');

        $articleOne = $manager->article([
            'headline' => 'Laravel Basics',
        ]);

        $articleTwo = $manager->create(ArticleSchema::class)
            ->id('https://example.com/#article-two')
            ->headline('Advanced Laravel');

        $articleOne->isPartOf($page);
        $articleTwo->isPartOf($page);

        $result = $manager->render();

        $this->assertSame(
            2,
            substr_count(
                $result,
                '"@type":"Article"'
            )
        );

        $this->assertSame(
            1,
            substr_count(
                $result,
                '"@type":"WebPage"'
            )
        );

        $this->assertSame(
            2,
            substr_count(
                $result,
                '"isPartOf":{"@id":"https://example.com/test/#webpage"}'
            )
        );
    }
}