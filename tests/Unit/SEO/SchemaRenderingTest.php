<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\SchemaContext;
use Therajatspace\Larakit\SEO\Schema\SchemaManager;
use Therajatspace\Larakit\SEO\Schema\SchemaRelationshipResolver;
use PHPUnit\Framework\Attributes\DataProvider;

class SchemaRenderingTest extends TestCase
{
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

    #[DataProvider('schemaFactoryProvider')]
    public function test_schema_factory_registers_and_renders_schema(
        string $factory,
        string $expectedType,
        array $data = []
    ): void {
        $manager = $this->createSchemaManager();

        $schema = $manager->{$factory}($data);

        $this->assertSame(
            1,
            $manager->count(),
            "{$factory}() did not register the schema."
        );

        $schemaData = $schema->toArray();

        $this->assertSame(
            $expectedType,
            $schemaData['@type']
        );

        $rendered = $manager->render();

        $this->assertStringContainsString(
            '<script type="application/ld+json">',
            $rendered
        );

        $this->assertStringContainsString(
            '"@type":"' . $expectedType . '"',
            $rendered
        );
    }

    public static function schemaFactoryProvider(): array
    {
        return [
            'Article' => [
                'article',
                'Article',
                [
                    'headline' => 'Test Article',
                ],
            ],

            'Breadcrumb' => [
                'breadcrumbs',
                'BreadcrumbList',
                [],
            ],

            'FAQPage' => [
                'faqPage',
                'FAQPage',
                [
                    'questions' => [
                        [
                            'question' => 'What is LaraKit?',
                            'answer' => 'A Laravel SEO toolkit.',
                        ],
                    ],
                ],
            ],

            'LocalBusiness' => [
                'localBusiness',
                'LocalBusiness',
                [
                    'name' => 'LaraKit Office',
                ],
            ],

            'Organization' => [
                'organization',
                'Organization',
                [
                    'name' => 'LaraKit',
                ],
            ],

            'Person' => [
                'person',
                'Person',
                [
                    'name' => 'John Doe',
                ],
            ],

            'Product' => [
                'product',
                'Product',
                [
                    'name' => 'LaraKit Pro',
                ],
            ],

            'WebPage' => [
                'webPage',
                'WebPage',
                [
                    'name' => 'LaraKit',
                ],
            ],

            'WebSite' => [
                'website',
                'WebSite',
                [
                    'name' => 'LaraKit',
                ],
            ],
        ];
    }
}