<?php

namespace Sidd2604\Larakit\SEO\OpenGraph;

class OpenGraphManager
{
    protected ?string $title = null;

    public function title(string $title): static
    {
        $this->title = $title;

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

        return $html;
    }
}