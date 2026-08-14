<?php

namespace Sidd2604\Larakit\SEO\OpenGraph;

class OpenGraphManager
{
    protected ?string $title = null;

    protected ?string $description = null;

    protected ?string $type = null;

    protected ?string $url = null;

    protected ?string $image = null;
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

    public function image(string $image): static
    {
        $this->image = $image;

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

        if ($this->image) {
            $html .= '<meta property="og:image" content="'
                . htmlspecialchars($this->image, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }

        return $html;
    }
}