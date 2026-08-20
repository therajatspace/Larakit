<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\FAQPageSchema;

class FAQPageSchemaTest extends TestCase
{
    public function test_faq_page_has_correct_type(): void
    {
        $faq = new FAQPageSchema();

        $data = $faq->toArray();

        $this->assertSame(
            'FAQPage',
            $data['@type']
        );
    }

    public function test_faq_page_initializes_main_entity_as_empty_array(): void
    {
        $faq = new FAQPageSchema();

        $data = $faq->toArray();

        $this->assertSame(
            [],
            $data['mainEntity']
        );
    }

    public function test_add_question_creates_correct_structure(): void
    {
        $faq = new FAQPageSchema();

        $data = $faq
            ->addQuestion(
                'What is LaraKit?',
                'LaraKit is a Laravel SEO toolkit.'
            )
            ->toArray();

        $this->assertCount(
            1,
            $data['mainEntity']
        );

        $this->assertSame(
            [
                '@type' => 'Question',
                'name' => 'What is LaraKit?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'LaraKit is a Laravel SEO toolkit.',
                ],
            ],
            $data['mainEntity'][0]
        );
    }

    public function test_multiple_questions_can_be_added(): void
    {
        $faq = new FAQPageSchema();

        $faq
            ->addQuestion(
                'What is LaraKit?',
                'A Laravel SEO toolkit.'
            )
            ->addQuestion(
                'Which Laravel versions are supported?',
                'LaraKit supports the supported Laravel versions defined by the package.'
            );

        $data = $faq->toArray();

        $this->assertCount(
            2,
            $data['mainEntity']
        );

        $this->assertSame(
            'What is LaraKit?',
            $data['mainEntity'][0]['name']
        );

        $this->assertSame(
            'Which Laravel versions are supported?',
            $data['mainEntity'][1]['name']
        );
    }

    public function test_add_question_trims_question_and_answer(): void
    {
        $faq = new FAQPageSchema();

        $data = $faq
            ->addQuestion(
                '  What is LaraKit?  ',
                '  A Laravel SEO toolkit.  '
            )
            ->toArray();

        $this->assertSame(
            'What is LaraKit?',
            $data['mainEntity'][0]['name']
        );

        $this->assertSame(
            'A Laravel SEO toolkit.',
            $data['mainEntity'][0]['acceptedAnswer']['text']
        );
    }

    public function test_empty_question_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $faq = new FAQPageSchema();

        $faq->addQuestion(
            '',
            'An answer.'
        );
    }

    public function test_whitespace_only_question_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $faq = new FAQPageSchema();

        $faq->addQuestion(
            '   ',
            'An answer.'
        );
    }

    public function test_empty_answer_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $faq = new FAQPageSchema();

        $faq->addQuestion(
            'A question?',
            ''
        );
    }

    public function test_whitespace_only_answer_is_rejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $faq = new FAQPageSchema();

        $faq->addQuestion(
            'A question?',
            '   '
        );
    }

    public function test_questions_method_adds_multiple_questions(): void
    {
        $faq = new FAQPageSchema();

        $faq->questions([
            [
                'question' => 'What is LaraKit?',
                'answer' => 'A Laravel SEO toolkit.',
            ],
            [
                'question' => 'Is LaraKit open source?',
                'answer' => 'Yes.',
            ],
        ]);

        $this->assertSame(
            2,
            $faq->questionCount()
        );
    }

    public function test_questions_method_returns_fluent_instance(): void
    {
        $faq = new FAQPageSchema();

        $result = $faq->questions([
            [
                'question' => 'What is LaraKit?',
                'answer' => 'A Laravel SEO toolkit.',
            ],
        ]);

        $this->assertSame(
            $faq,
            $result
        );
    }

    public function test_questions_method_rejects_non_array_question(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $faq = new FAQPageSchema();

        $faq->questions([
            'invalid question',
        ]);
    }

    public function test_questions_method_requires_question_key(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $faq = new FAQPageSchema();

        $faq->questions([
            [
                'answer' => 'An answer.',
            ],
        ]);
    }

    public function test_questions_method_requires_answer_key(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $faq = new FAQPageSchema();

        $faq->questions([
            [
                'question' => 'A question?',
            ],
        ]);
    }

    public function test_questions_method_rejects_empty_question(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $faq = new FAQPageSchema();

        $faq->questions([
            [
                'question' => '',
                'answer' => 'An answer.',
            ],
        ]);
    }

    public function test_questions_method_rejects_empty_answer(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $faq = new FAQPageSchema();

        $faq->questions([
            [
                'question' => 'A question?',
                'answer' => '',
            ],
        ]);
    }

    public function test_empty_questions_array_is_allowed(): void
    {
        $faq = new FAQPageSchema();

        $faq->questions([]);

        $this->assertSame(
            0,
            $faq->questionCount()
        );
    }

    public function test_get_questions_returns_questions(): void
    {
        $faq = new FAQPageSchema();

        $faq->addQuestion(
            'What is LaraKit?',
            'A Laravel SEO toolkit.'
        );

        $questions = $faq->getQuestions();

        $this->assertCount(
            1,
            $questions
        );

        $this->assertSame(
            'What is LaraKit?',
            $questions[0]['name']
        );
    }

    public function test_question_count_returns_correct_count(): void
    {
        $faq = new FAQPageSchema();

        $this->assertSame(
            0,
            $faq->questionCount()
        );

        $faq->addQuestion(
            'Question one?',
            'Answer one.'
        );

        $this->assertSame(
            1,
            $faq->questionCount()
        );

        $faq->addQuestion(
            'Question two?',
            'Answer two.'
        );

        $this->assertSame(
            2,
            $faq->questionCount()
        );
    }

    public function test_from_array_supports_faq_data(): void
    {
        $faq = new FAQPageSchema();

        $data = $faq
            ->fromArray([
                'name' => 'Frequently Asked Questions',
                'description' => 'Common questions about LaraKit.',
                'url' => 'https://example.com/faq',
                'questions' => [
                    [
                        'question' => 'What is LaraKit?',
                        'answer' => 'A Laravel SEO toolkit.',
                    ],
                    [
                        'question' => 'Is it open source?',
                        'answer' => 'Yes.',
                    ],
                ],
            ])
            ->toArray();

        $this->assertSame(
            'FAQPage',
            $data['@type']
        );

        $this->assertSame(
            'Frequently Asked Questions',
            $data['name']
        );

        $this->assertSame(
            'Common questions about LaraKit.',
            $data['description']
        );

        $this->assertSame(
            'https://example.com/faq',
            $data['url']
        );

        $this->assertCount(
            2,
            $data['mainEntity']
        );
    }

    public function test_from_array_preserves_faq_page_type(): void
    {
        $faq = new FAQPageSchema();

        $data = $faq
            ->fromArray([
                'name' => 'FAQ',
            ])
            ->toArray();

        $this->assertSame(
            'FAQPage',
            $data['@type']
        );
    }

    public function test_inherited_schema_properties_work(): void
    {
        $faq = new FAQPageSchema();

        $data = $faq
            ->name('FAQ')
            ->description('Frequently asked questions.')
            ->url('https://example.com/faq')
            ->toArray();

        $this->assertSame(
            'FAQ',
            $data['name']
        );

        $this->assertSame(
            'Frequently asked questions.',
            $data['description']
        );

        $this->assertSame(
            'https://example.com/faq',
            $data['url']
        );
    }

    public function test_add_question_is_fluent(): void
    {
        $faq = new FAQPageSchema();

        $result = $faq->addQuestion(
            'What is LaraKit?',
            'A Laravel SEO toolkit.'
        );

        $this->assertSame(
            $faq,
            $result
        );
    }

    public function test_nested_question_does_not_contain_context(): void
    {
        $faq = new FAQPageSchema();

        $data = $faq
            ->addQuestion(
                'What is LaraKit?',
                'A Laravel SEO toolkit.'
            )
            ->toArray();

        $this->assertArrayNotHasKey(
            '@context',
            $data['mainEntity'][0]
        );

        $this->assertArrayNotHasKey(
            '@context',
            $data['mainEntity'][0]['acceptedAnswer']
        );
    }
}