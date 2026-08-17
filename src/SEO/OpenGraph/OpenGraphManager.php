<?php

namespace Therajatspace\Larakit\SEO\OpenGraph;

class OpenGraphManager
{
    protected ?string $title = null;

    protected ?string $description = null;

    protected ?string $type = null;

    protected ?string $url = null;

    protected array $images = [];
    protected array $allowedTypes = [
        'website',
        'article',
        'book',
        'profile',
        'music.song',
        'music.album',
        'music.playlist',
        'music.radio_station',
        'video.movie',
        'video.episode',
        'video.tv_show',
        'video.other',
    ];
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

    public function type(string $type): static
    {
        if (!in_array($type, $this->allowedTypes, true)) {
            throw new \InvalidArgumentException(
                "Invalid Open Graph type: {$type}"
            );
        }

        $this->type = $type;

        return $this;
    }

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function image(
        string $url,
        ?string $alt = null,
        ?int $width = null,
        ?int $height = null
    ): static {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(
                "Invalid Open Graph image URL: {$url}"
            );
        }

        if ($width !== null && $width <= 0) {
            throw new \InvalidArgumentException(
                'Open Graph image width must be greater than 0.'
            );
        }

        if ($height !== null && $height <= 0) {
            throw new \InvalidArgumentException(
                'Open Graph image height must be greater than 0.'
            );
        }

        $this->images[] = [
            'url' => $url,
            'alt' => $alt,
            'width' => $width,
            'height' => $height,
        ];

        return $this;
    }
    public function getFirstImage(): ?array
    {
        return $this->images[0] ?? null;
    }

    public function inherit(
        ?string $title = null,
        ?string $description = null,
        ?string $url = null
    ): static {
        if ($this->title === null && $title !== null) {
            $this->title = $title;
        }

        if ($this->description === null && $description !== null) {
            $this->description = $description;
        }

        if ($this->url === null && $url !== null) {
            $this->url = $url;
        }

        return $this;
    }

    public function render(): string
    {
        $html = '';

        if ($this->title) {
            $html .= '<meta property="og:title" content="'
                . htmlspecialchars($this->title, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }

        if ($this->description) {
            $html .= '<meta property="og:description" content="'
                . htmlspecialchars($this->description, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }

        if ($this->type) {
            $html .= '<meta property="og:type" content="'
                . htmlspecialchars($this->type, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }

        if ($this->url) {
            $html .= '<meta property="og:url" content="'
                . htmlspecialchars($this->url, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }

        foreach ($this->images as $image) {

            $html .= '<meta property="og:image" content="'
                . htmlspecialchars($image['url'], ENT_QUOTES, 'UTF-8')
                . "\">\n";

            if ($image['alt']) {
                $html .= '<meta property="og:image:alt" content="'
                    . htmlspecialchars($image['alt'], ENT_QUOTES, 'UTF-8')
                    . "\">\n";
            }

            if ($image['width']) {
                $html .= '<meta property="og:image:width" content="'
                    . $image['width']
                    . "\">\n";
            }

            if ($image['height']) {
                $html .= '<meta property="og:image:height" content="'
                    . $image['height']
                    . "\">\n";
            }
        }

        return $html;
    }
}