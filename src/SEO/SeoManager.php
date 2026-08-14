<?php

namespace Sidd2604\Larakit\SEO;

class SeoManager
{
    protected ?string $title = null;

    protected ?string $description = null;

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

    public function render(): string
    {
        $html = '';

        if ($this->title) {
            $html .= '<title>' . htmlspecialchars($this->title) . "</title>\n";
        }

        if ($this->description) {
            $html .= '<meta name="description" content="' .
                htmlspecialchars($this->description) .
                "\">\n";
        }

        return $html;
    }
}