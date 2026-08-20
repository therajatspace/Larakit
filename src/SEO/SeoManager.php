<?php

namespace Therajatspace\Larakit\SEO;
use Therajatspace\Larakit\SEO\OpenGraph\OpenGraphManager;
use Therajatspace\Larakit\SEO\Twitter\TwitterCardManager;
use Therajatspace\Larakit\SEO\Schema\SchemaManager;
use Therajatspace\Larakit\SEO\Schema\SchemaObject;
use Therajatspace\Larakit\SEO\Schema\ArticleSchema;
use Therajatspace\Larakit\SEO\Schema\BreadcrumbSchema;
use Therajatspace\Larakit\SEO\Schema\OrganizationSchema;
use Therajatspace\Larakit\SEO\Schema\WebSiteSchema;
use Illuminate\Support\Facades\Config;
use Therajatspace\Larakit\SEO\Schema\SchemaConfigurator;
use Therajatspace\Larakit\SEO\Schema\ProductSchema;
use Therajatspace\Larakit\SEO\Schema\PersonSchema;
use Therajatspace\Larakit\SEO\Schema\FAQPageSchema;
use Therajatspace\Larakit\SEO\Schema\WebPageSchema;
use Therajatspace\Larakit\SEO\Schema\LocalBusinessSchema;

class SeoManager
{
    protected ?string $title = null;

    protected array $meta = [];

    protected ?string $canonical = null;
    protected ?string $favicon = null;

    protected ?string $faviconType = null;

    protected ?string $appleTouchIcon = null;

    protected ?string $manifest = null;

    protected bool $disableFavicon = false;

    protected bool $disableAppleTouchIcon = false;

    protected bool $disableManifest = false;
    protected array $head = [];
    protected OpenGraphManager $openGraph;

    protected TwitterCardManager $twitter;

    protected SchemaManager $schema;
    protected SchemaConfigurator $schemaConfigurator;

    public function __construct(
        OpenGraphManager $openGraph,
        TwitterCardManager $twitter,
        SchemaManager $schema,
        SchemaConfigurator $schemaConfigurator,

        array $defaults = [],
        array $head = []
    ) {
        $this->openGraph = $openGraph;
        $this->twitter = $twitter;
        $this->schema = $schema;
        $this->schemaConfigurator = $schemaConfigurator;
        $this->head = $head;

        $this->title = $defaults['title'] ?? null;

        if (!empty($defaults['description'])) {
            $this->meta['description'] = $defaults['description'];
        }

        if (!empty($defaults['robots'])) {
            $this->meta['robots'] = $defaults['robots'];
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
    public function favicon(string $url, ?string $type = null): static
    {
        $this->favicon = $url;
        $this->faviconType = $type;
        $this->disableFavicon = false;

        return $this;
    }

    public function appleTouchIcon(string $url): static
    {
        $this->appleTouchIcon = $url;
        $this->disableAppleTouchIcon = false;

        return $this;
    }

    public function manifest(string $url): static
    {
        $this->manifest = $url;
        $this->disableManifest = false;

        return $this;
    }
    public function withoutFavicon(): static
    {
        $this->disableFavicon = true;

        return $this;
    }

    public function withoutAppleTouchIcon(): static
    {
        $this->disableAppleTouchIcon = true;

        return $this;
    }

    public function withoutManifest(): static
    {
        $this->disableManifest = true;

        return $this;
    }
    public function openGraph(): OpenGraphManager
    {
        return $this->openGraph;
    }
    public function twitter(): TwitterCardManager
    {
        return $this->twitter;
    }
    public function schema(): SchemaObject
    {
        return $this->schema->create();
    }
    public function article(): ArticleSchema
    {
        return $this->schema->article();
    }
    public function breadcrumbs(): BreadcrumbSchema
    {
        return $this->schema->breadcrumbs();
    }
    public function organization(): OrganizationSchema
    {
        return $this->schema->organization();
    }
    public function website(): WebSiteSchema
    {
        return $this->schema->website();
    }
    public function configureSchemas(array $config): static
    {
        $this->schemaConfigurator->configure($config);

        return $this;
    }

    public function product(): ProductSchema
    {
        return $this->schema->product();
    }

    public function person(): PersonSchema
    {
        return $this->schema->person();
    }

    public function faqPage(): FAQPageSchema
    {
        return $this->schema->faqPage();
    }

    public function webPage(): WebPageSchema
    {
        return $this->schema->webPage();
    }

    public function localBusiness(): LocalBusinessSchema
    {
        return $this->schema->localBusiness();
    }

    protected function renderFavicon(): string
    {
        if ($this->disableFavicon) {
            return '';
        }

        if ($this->favicon !== null) {
            $html = '<link rel="icon" href="'
                . htmlspecialchars($this->favicon, ENT_QUOTES, 'UTF-8')
                . '"';

            if ($this->faviconType !== null) {
                $html .= ' type="'
                    . htmlspecialchars($this->faviconType, ENT_QUOTES, 'UTF-8')
                    . '"';
            }

            return $html . ">\n";
        }

        $config = $this->head['favicon'] ?? [];

        if (
            !($config['enabled'] ?? false) ||
            empty($config['url'])
        ) {
            return '';
        }

        $html = '<link rel="icon" href="'
            . htmlspecialchars($config['url'], ENT_QUOTES, 'UTF-8')
            . '"';

        if (!empty($config['type'])) {
            $html .= ' type="'
                . htmlspecialchars($config['type'], ENT_QUOTES, 'UTF-8')
                . '"';
        }

        return $html . ">\n";
    }

    protected function renderAppleTouchIcon(): string
    {
        if ($this->disableAppleTouchIcon) {
            return '';
        }

        $url = $this->appleTouchIcon;

        if ($url === null) {
            $config = $this->head['apple_touch_icon'] ?? [];

            if (
                !($config['enabled'] ?? false) ||
                empty($config['url'])
            ) {
                return '';
            }

            $url = $config['url'];
        }

        return '<link rel="apple-touch-icon" href="'
            . htmlspecialchars($url, ENT_QUOTES, 'UTF-8')
            . "\">\n";
    }

    protected function renderManifest(): string
    {
        if ($this->disableManifest) {
            return '';
        }

        $url = $this->manifest;

        if ($url === null) {
            $config = $this->head['manifest'] ?? [];

            if (
                !($config['enabled'] ?? false) ||
                empty($config['url'])
            ) {
                return '';
            }

            $url = $config['url'];
        }

        return '<link rel="manifest" href="'
            . htmlspecialchars($url, ENT_QUOTES, 'UTF-8')
            . "\">\n";
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

        $html .= $this->renderFavicon();
        $html .= $this->renderAppleTouchIcon();
        $html .= $this->renderManifest();

        $this->openGraph->inherit(
            $this->title,
            $this->meta['description'] ?? null,
            $this->canonical
        );
        $this->twitter->inherit(
            $this->title,
            $this->meta['description'] ?? null
        );
        $this->twitter->inheritImage(
            $this->openGraph->getFirstImage()
        );
        $html .= $this->openGraph->render();
        $html .= $this->twitter->render();
        $html .= $this->schema->render();

        return $html;
    }
}