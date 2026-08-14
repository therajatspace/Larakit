<?php

namespace Sidd2604\Larakit\SEO;

class SeoManager
{
    protected ?string $title = null;

    protected array $meta = [];

    protected ?string $canonical = null;

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): static
    {
        $this->meta['description'] = $description;

        return $this;
    }

    public function keywords(string $keywords): static
    {
        $this->meta['keywords'] = $keywords;

        return $this;
    }

    public function robots(string $robots): static
    {
        $this->meta['robots'] = $robots;

        return $this;
    }

    public function canonical(string $url): static
    {
        $this->canonical = $url;

        return $this;
    }

    public function render(): string
    {
        $html = '';

        if ($this->title) {
            $html .= '<title>'
                . htmlspecialchars($this->title, ENT_QUOTES, 'UTF-8')
                . "</title>\n";
        }

        foreach ($this->meta as $name => $content) {
            $html .= '<meta name="'
                . htmlspecialchars($name, ENT_QUOTES, 'UTF-8')
                . '" content="'
                . htmlspecialchars($content, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }

        if ($this->canonical) {
            $html .= '<link rel="canonical" href="'
                . htmlspecialchars($this->canonical, ENT_QUOTES, 'UTF-8')
                . "\">\n";
        }

        return $html;
    }
}