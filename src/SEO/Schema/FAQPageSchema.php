<?php

namespace Therajatspace\Larakit\SEO\Schema;

class FAQPageSchema extends SchemaObject
{
    public function __construct()
    {
        $this->data['@type'] = 'FAQPage';
        $this->data['mainEntity'] = [];
    }

    /**
     * Add a question and its accepted answer.
     */
    public function addQuestion(
        string $question,
        string $answer
    ): static {
        $question = trim($question);
        $answer = trim($answer);

        if ($question === '') {
            throw new \InvalidArgumentException(
                'FAQ question cannot be empty.'
            );
        }

        if ($answer === '') {
            throw new \InvalidArgumentException(
                'FAQ answer cannot be empty.'
            );
        }

        $this->data['mainEntity'][] = [
            '@type' => 'Question',
            'name' => $question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $answer,
            ],
        ];

        return $this;
    }

    /**
     * Add multiple questions.
     *
     * Each question must contain:
     *
     * [
     *     'question' => 'Question text',
     *     'answer' => 'Answer text',
     * ]
     */
    public function questions(array $questions): static
    {
        foreach ($questions as $question) {
            if (!is_array($question)) {
                throw new \InvalidArgumentException(
                    'Each FAQ question must be an array.'
                );
            }

            if (!array_key_exists('question', $question)) {
                throw new \InvalidArgumentException(
                    'Each FAQ question must contain a "question" key.'
                );
            }

            if (!array_key_exists('answer', $question)) {
                throw new \InvalidArgumentException(
                    'Each FAQ question must contain an "answer" key.'
                );
            }

            $this->addQuestion(
                $question['question'],
                $question['answer']
            );
        }

        return $this;
    }

    /**
     * Return all FAQ questions.
     */
    public function getQuestions(): array
    {
        return $this->data['mainEntity'];
    }

    /**
     * Return the number of FAQ questions.
     */
    public function questionCount(): int
    {
        return count($this->data['mainEntity']);
    }

    public function fromArray(array $data): static
    {
        if (isset($data['name'])) {
            $this->name($data['name']);
        }

        if (isset($data['description'])) {
            $this->description($data['description']);
        }

        if (isset($data['url'])) {
            $this->url($data['url']);
        }

        if (isset($data['questions'])) {
            $this->questions($data['questions']);
        }

        return $this;
    }
}