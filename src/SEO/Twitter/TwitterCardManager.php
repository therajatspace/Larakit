<?php

namespace Sidd2604\Larakit\SEO\Twitter;

class TwitterCardManager
{
    protected ?string $card = null;

    protected ?string $title = null;
    protected ?string $description = null;
    protected ?string $image = null;

    protected ?string $imageAlt = null;
    protected ?string $site = null;

    protected ?string $creator = null;
    protected array $allowedCards = [
        'summary',
        'summary_large_image',
        'app',
        'player',
    ];

    public function card(string $card): static
    {
        if (!in_array($card, $this->allowedCards, true)) {
            throw new \InvalidArgumentException(
                "Invalid Twitter card type: {$card}"
            );
        }

        $this->card = $card;

        return $this;
    }

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }
    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }
    public function image(
        string $url,
        ?string $alt = null
    ): static {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(
                "Invalid Twitter image URL: {$url}"
            );
        }

        $this->image = $url;
        $this->imageAlt = $alt;

        return $this;
    }
    public function site(string $site): static
    {
        $this->site = $site;

        return $this;
    }
    public function creator(string $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    public function render(): string
    {
        $html = '';

        if ($this->card) {
            $html .= '<meta name="twitter:card" content="'
                . htmlspecialchars($this->card, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }

        if ($this->title) {
            $html .= '<meta name="twitter:title" content="'
                . htmlspecialchars($this->title, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }
        if ($this->description) {
            $html .= '<meta name="twitter:description" content="'
                . htmlspecialchars($this->description, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }
        if ($this->image) {
            $html .= '<meta name="twitter:image" content="'
                . htmlspecialchars($this->image, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }

        if ($this->imageAlt) {
            $html .= '<meta name="twitter:image:alt" content="'
                . htmlspecialchars($this->imageAlt, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }
        if ($this->site) {
            $html .= '<meta name="twitter:site" content="'
                . htmlspecialchars($this->site, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }

        if ($this->creator) {
            $html .= '<meta name="twitter:creator" content="'
                . htmlspecialchars($this->creator, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }

        return $html;
    }

    public function inherit(
        ?string $title = null,
        ?string $description = null
    ): static {
        if ($this->title === null && $title !== null) {
            $this->title = $title;
        }

        if ($this->description === null && $description !== null) {
            $this->description = $description;
        }

        return $this;
    }

    public function inheritImage(?array $image): static
    {
        if ($this->image === null && !empty($image['url'])) {
            $this->image = $image['url'];
            $this->imageAlt = $image['alt'] ?? null;
        }

        return $this;
    }
}