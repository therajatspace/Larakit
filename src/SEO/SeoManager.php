<?php

namespace Sidd2604\Larakit\SEO;

class SeoManager
{
    private ?string $title = null;

    private ?string $description = null;

    private ?string $canonical = null;

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }


    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function render(): string
    {
        $html = '';

        if ($this->title !== null) {
            $html .= '<title>' . htmlspecialchars($this->title) . '</title>' . PHP_EOL;
        }

        if ($this->description !== null) {
            $html .= '<meta name="description" content="' .
                htmlspecialchars($this->description) .
                '">' . PHP_EOL;
        }

        return $html;
    }
}