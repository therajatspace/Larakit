<?php

namespace Therajatspace\Larakit\SEO\Schema;

use Therajatspace\Larakit\SEO\Support\DateValidator;
use Therajatspace\Larakit\SEO\Support\UrlValidator;

class PersonSchema extends SchemaObject
{
    public function __construct()
    {
        $this->data['@type'] = 'Person';
    }

    public function image(string $url): static
    {
        UrlValidator::validate($url);

        $this->data['image'] = $url;

        return $this;
    }

    public function sameAs(string $url): static
    {
        UrlValidator::validate($url);

        $this->data['sameAs'] = $url;

        return $this;
    }

    public function givenName(string $givenName): static
    {
        $this->data['givenName'] = $givenName;

        return $this;
    }

    public function familyName(string $familyName): static
    {
        $this->data['familyName'] = $familyName;

        return $this;
    }

    public function additionalName(string $additionalName): static
    {
        $this->data['additionalName'] = $additionalName;

        return $this;
    }

    public function alternateName(string $alternateName): static
    {
        $this->data['alternateName'] = $alternateName;

        return $this;
    }

    public function honorificPrefix(string $prefix): static
    {
        $this->data['honorificPrefix'] = $prefix;

        return $this;
    }

    public function honorificSuffix(string $suffix): static
    {
        $this->data['honorificSuffix'] = $suffix;

        return $this;
    }

    public function jobTitle(string $jobTitle): static
    {
        $this->data['jobTitle'] = $jobTitle;

        return $this;
    }

    public function email(string $email): static
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                "Invalid email address: {$email}"
            );
        }

        $this->data['email'] = $email;

        return $this;
    }

    public function telephone(string $telephone): static
    {
        $this->data['telephone'] = $telephone;

        return $this;
    }

    public function gender(string $gender): static
    {
        $this->data['gender'] = $gender;

        return $this;
    }

    public function birthDate(string $date): static
    {
        DateValidator::validate($date);

        $this->data['birthDate'] = $date;

        return $this;
    }

    public function deathDate(string $date): static
    {
        DateValidator::validate($date);

        $this->data['deathDate'] = $date;

        return $this;
    }

    public function nationality(string $nationality): static
    {
        $this->data['nationality'] = $nationality;

        return $this;
    }

    public function knowsAbout(string|array $topics): static
    {
        $this->data['knowsAbout'] = $topics;

        return $this;
    }

    public function knowsLanguage(string|array $languages): static
    {
        $this->data['knowsLanguage'] = $languages;

        return $this;
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

        if (isset($data['image'])) {
            $this->image($data['image']);
        }

        if (isset($data['sameAs'])) {
            $this->sameAs($data['sameAs']);
        }

        if (isset($data['givenName'])) {
            $this->givenName($data['givenName']);
        }

        if (isset($data['familyName'])) {
            $this->familyName($data['familyName']);
        }

        if (isset($data['additionalName'])) {
            $this->additionalName($data['additionalName']);
        }

        if (isset($data['alternateName'])) {
            $this->alternateName($data['alternateName']);
        }

        if (isset($data['honorificPrefix'])) {
            $this->honorificPrefix($data['honorificPrefix']);
        }

        if (isset($data['honorificSuffix'])) {
            $this->honorificSuffix($data['honorificSuffix']);
        }

        if (isset($data['jobTitle'])) {
            $this->jobTitle($data['jobTitle']);
        }

        if (isset($data['email'])) {
            $this->email($data['email']);
        }

        if (isset($data['telephone'])) {
            $this->telephone($data['telephone']);
        }

        if (isset($data['gender'])) {
            $this->gender($data['gender']);
        }

        if (isset($data['birthDate'])) {
            $this->birthDate($data['birthDate']);
        }

        if (isset($data['deathDate'])) {
            $this->deathDate($data['deathDate']);
        }

        if (isset($data['nationality'])) {
            $this->nationality($data['nationality']);
        }

        if (isset($data['knowsAbout'])) {
            $this->knowsAbout($data['knowsAbout']);
        }

        if (isset($data['knowsLanguage'])) {
            $this->knowsLanguage($data['knowsLanguage']);
        }

        return $this;
    }
}