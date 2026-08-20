<?php

namespace Therajatspace\Larakit\Tests\Unit\SEO;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\SEO\Schema\PersonSchema;

class PersonSchemaTest extends TestCase
{
    public function test_person_has_person_type(): void
    {
        $person = new PersonSchema();

        $this->assertSame(
            'Person',
            $person->toArray()['@type']
        );
    }

    public function test_person_stores_identity_fields(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->name('Siddharth Sharma')
            ->givenName('Siddharth')
            ->familyName('Sharma')
            ->additionalName('Kumar')
            ->alternateName('Siddharth')
            ->honorificPrefix('Mr.')
            ->honorificSuffix('B.Tech')
            ->toArray();

        $this->assertSame('Siddharth Sharma', $data['name']);
        $this->assertSame('Siddharth', $data['givenName']);
        $this->assertSame('Sharma', $data['familyName']);
        $this->assertSame('Kumar', $data['additionalName']);
        $this->assertSame('Siddharth', $data['alternateName']);
        $this->assertSame('Mr.', $data['honorificPrefix']);
        $this->assertSame('B.Tech', $data['honorificSuffix']);
    }

    public function test_person_stores_professional_fields(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->jobTitle('Laravel Developer')
            ->knowsAbout([
                'Laravel',
                'PHP',
                'MySQL',
            ])
            ->knowsLanguage([
                'English',
                'Hindi',
            ])
            ->toArray();

        $this->assertSame(
            'Laravel Developer',
            $data['jobTitle']
        );

        $this->assertSame(
            [
                'Laravel',
                'PHP',
                'MySQL',
            ],
            $data['knowsAbout']
        );

        $this->assertSame(
            [
                'English',
                'Hindi',
            ],
            $data['knowsLanguage']
        );
    }

    public function test_person_stores_contact_fields(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->email('siddharth@example.com')
            ->telephone('+91-9876543210')
            ->toArray();

        $this->assertSame(
            'siddharth@example.com',
            $data['email']
        );

        $this->assertSame(
            '+91-9876543210',
            $data['telephone']
        );
    }

    public function test_invalid_email_throws_exception(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $person = new PersonSchema();

        $person->email('not-an-email');
    }

    public function test_person_stores_personal_fields(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->gender('Male')
            ->nationality('Indian')
            ->birthDate('1999-01-15')
            ->deathDate('2099-01-15')
            ->toArray();

        $this->assertSame('Male', $data['gender']);
        $this->assertSame('Indian', $data['nationality']);
        $this->assertSame('1999-01-15', $data['birthDate']);
        $this->assertSame('2099-01-15', $data['deathDate']);
    }

    public function test_invalid_birth_date_throws_exception(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $person = new PersonSchema();

        $person->birthDate('not-a-date');
    }

    public function test_invalid_death_date_throws_exception(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $person = new PersonSchema();

        $person->deathDate('not-a-date');
    }

    public function test_invalid_image_url_throws_exception(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $person = new PersonSchema();

        $person->image('not-a-url');
    }

    public function test_invalid_same_as_url_throws_exception(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $person = new PersonSchema();

        $person->sameAs('not-a-url');
    }

    public function test_person_stores_image_and_same_as(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->image('https://example.com/images/siddharth.jpg')
            ->sameAs('https://github.com/example')
            ->toArray();

        $this->assertSame(
            'https://example.com/images/siddharth.jpg',
            $data['image']
        );

        $this->assertSame(
            'https://github.com/example',
            $data['sameAs']
        );
    }

    public function test_from_array_uses_typed_setters(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->fromArray([
                'name' => 'Siddharth Sharma',
                'givenName' => 'Siddharth',
                'familyName' => 'Sharma',
                'jobTitle' => 'Laravel Developer',
                'email' => 'siddharth@example.com',
                'telephone' => '+91-9876543210',
                'birthDate' => '1999-01-15',
                'image' => 'https://example.com/person.jpg',
                'sameAs' => 'https://github.com/example',
            ])
            ->toArray();

        $this->assertSame(
            'Person',
            $data['@type']
        );

        $this->assertSame(
            'Siddharth Sharma',
            $data['name']
        );

        $this->assertSame(
            'Siddharth',
            $data['givenName']
        );

        $this->assertSame(
            'Sharma',
            $data['familyName']
        );

        $this->assertSame(
            'Laravel Developer',
            $data['jobTitle']
        );

        $this->assertSame(
            'siddharth@example.com',
            $data['email']
        );

        $this->assertSame(
            '1999-01-15',
            $data['birthDate']
        );
    }

    public function test_from_array_validates_email(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $person = new PersonSchema();

        $person->fromArray([
            'email' => 'invalid-email',
        ]);
    }

    public function test_from_array_validates_date(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        $person = new PersonSchema();

        $person->fromArray([
            'birthDate' => 'banana',
        ]);
    }

    public function test_person_inherits_schema_object_methods(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->name('Siddharth Sharma')
            ->description('Laravel developer and software engineer.')
            ->url('https://example.com/about')
            ->toArray();

        $this->assertSame(
            'Siddharth Sharma',
            $data['name']
        );

        $this->assertSame(
            'Laravel developer and software engineer.',
            $data['description']
        );

        $this->assertSame(
            'https://example.com/about',
            $data['url']
        );
    }

    public function test_person_setters_are_fluent(): void
    {
        $person = new PersonSchema();

        $this->assertSame(
            $person,
            $person
                ->name('Siddharth Sharma')
                ->givenName('Siddharth')
                ->familyName('Sharma')
                ->jobTitle('Laravel Developer')
                ->telephone('+91-9876543210')
        );
    }
    public function test_knows_about_accepts_string(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->knowsAbout('Laravel')
            ->toArray();

        $this->assertSame(
            'Laravel',
            $data['knowsAbout']
        );
    }
    public function test_knows_about_accepts_array(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->knowsAbout([
                'Laravel',
                'PHP',
                'MySQL',
            ])
            ->toArray();

        $this->assertSame(
            [
                'Laravel',
                'PHP',
                'MySQL',
            ],
            $data['knowsAbout']
        );
    }
    public function test_knows_language_accepts_string(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->knowsLanguage('English')
            ->toArray();

        $this->assertSame(
            'English',
            $data['knowsLanguage']
        );
    }
    public function test_knows_language_accepts_array(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->knowsLanguage([
                'English',
                'Hindi',
            ])
            ->toArray();

        $this->assertSame(
            [
                'English',
                'Hindi',
            ],
            $data['knowsLanguage']
        );
    }
    public function test_from_array_supports_all_person_fields(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->fromArray([
                'name' => 'Siddharth Sharma',
                'description' => 'Software developer.',
                'url' => 'https://example.com/siddharth',
                'image' => 'https://example.com/siddharth.jpg',
                'sameAs' => 'https://github.com/siddharth',
                'givenName' => 'Siddharth',
                'familyName' => 'Sharma',
                'additionalName' => 'Kumar',
                'alternateName' => 'Sid',
                'honorificPrefix' => 'Mr.',
                'honorificSuffix' => 'B.Tech',
                'jobTitle' => 'Laravel Developer',
                'email' => 'siddharth@example.com',
                'telephone' => '+91-9876543210',
                'gender' => 'Male',
                'birthDate' => '2000-01-01',
                'deathDate' => '2099-01-01',
                'nationality' => 'Indian',
                'knowsAbout' => [
                    'PHP',
                    'Laravel',
                ],
                'knowsLanguage' => [
                    'English',
                    'Hindi',
                ],
            ])
            ->toArray();

        $this->assertSame('Person', $data['@type']);

        $this->assertSame(
            'Siddharth Sharma',
            $data['name']
        );

        $this->assertSame(
            'Software developer.',
            $data['description']
        );

        $this->assertSame(
            'https://example.com/siddharth',
            $data['url']
        );

        $this->assertSame(
            'https://example.com/siddharth.jpg',
            $data['image']
        );

        $this->assertSame(
            'https://github.com/siddharth',
            $data['sameAs']
        );

        $this->assertSame(
            'Siddharth',
            $data['givenName']
        );

        $this->assertSame(
            'Sharma',
            $data['familyName']
        );

        $this->assertSame(
            'Kumar',
            $data['additionalName']
        );

        $this->assertSame(
            'Sid',
            $data['alternateName']
        );

        $this->assertSame(
            'Mr.',
            $data['honorificPrefix']
        );

        $this->assertSame(
            'B.Tech',
            $data['honorificSuffix']
        );

        $this->assertSame(
            'Laravel Developer',
            $data['jobTitle']
        );

        $this->assertSame(
            'siddharth@example.com',
            $data['email']
        );

        $this->assertSame(
            '+91-9876543210',
            $data['telephone']
        );

        $this->assertSame(
            'Male',
            $data['gender']
        );

        $this->assertSame(
            '2000-01-01',
            $data['birthDate']
        );

        $this->assertSame(
            '2099-01-01',
            $data['deathDate']
        );

        $this->assertSame(
            'Indian',
            $data['nationality']
        );

        $this->assertSame(
            ['PHP', 'Laravel'],
            $data['knowsAbout']
        );

        $this->assertSame(
            ['English', 'Hindi'],
            $data['knowsLanguage']
        );
    }
    public function test_from_array_preserves_person_type(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->fromArray([
                'name' => 'Siddharth Sharma',
            ])
            ->toArray();

        $this->assertSame(
            'Person',
            $data['@type']
        );
    }
    public function test_empty_array_values_are_preserved(): void
    {
        $person = new PersonSchema();

        $data = $person
            ->knowsAbout([])
            ->knowsLanguage([])
            ->toArray();

        $this->assertSame(
            [],
            $data['knowsAbout']
        );

        $this->assertSame(
            [],
            $data['knowsLanguage']
        );
    }
}