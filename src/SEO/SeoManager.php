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

class SeoManager
{
    protected ?string $title = null;

    protected array $meta = [];

    protected ?string $canonical = null;
    protected OpenGraphManager $openGraph;

    protected TwitterCardManager $twitter;

    protected SchemaManager $schema;
    protected SchemaConfigurator $schemaConfigurator;

    public function __construct(
        OpenGraphManager $openGraph,
        TwitterCardManager $twitter,
        SchemaManager $schema,
        SchemaConfigurator $schemaConfigurator,

        array $defaults = []
    ) {
        $this->openGraph = $openGraph;
        $this->twitter = $twitter;
        $this->schema = $schema;
        $this->schemaConfigurator = $schemaConfigurator;



        //=================== TO BE DELETED========================//

        // if (config('larakit.seo.schema.auto', true)) {
        //     $this->loadConfiguredSchemas();
        // }

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


    // ================ TO BE DELETED========================//

    // protected function loadConfiguredSchemas(): void
    // {
    //     $organization = config(
    //         'larakit.seo.organization',
    //         []
    //     );

    //     if (!empty($organization['name'])) {
    //         $this->schema->organization($organization);
    //     }

    //     $website = config(
    //         'larakit.seo.website',
    //         []
    //     );

    //     if (!empty($website['name'])) {
    //         $this->schema->website($website);
    //     }
    // }

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