<?php

namespace Sidd2604\Larakit\SEO;
use Sidd2604\Larakit\SEO\OpenGraph\OpenGraphManager;


class SeoManager
{
    protected ?string $title = null;

    protected array $meta = [];

    protected ?string $canonical = null;
    protected OpenGraphManager $openGraph;

    public function __construct(OpenGraphManager $openGraph)
    {
        $this->openGraph = $openGraph;

        $this->title = config('larakit.seo.defaults.title');

        if ($description = config('larakit.seo.defaults.description')) {
            $this->meta['description'] = $description;
        }

        if ($robots = config('larakit.seo.defaults.robots')) {
            $this->meta['robots'] = $robots;
        }
    }

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
    public function openGraph(): OpenGraphManager
    {
        return $this->openGraph;
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

        $html .= $this->openGraph->render();

        return $html;
    }
}