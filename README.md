# LaraKit

A Laravel package providing reusable SEO utilities including meta tags,
Open Graph, Twitter Cards, JSON-LD structured data, Schema.org objects,
schema relationships, and a modular Artisan installer.

> **Current release:** `v1.5.0`

LaraKit is being built as a general-purpose Laravel toolkit. The package
is intentionally designed to stay understandable and practical: common
Laravel concepts, fluent PHP objects, Laravel's service container,
Laravel Prompts, and small focused classes are preferred over
unnecessary abstraction.

---

## Table of Contents

1.  [What is LaraKit?](#what-is-larakit)
2.  [Current Status](#current-status)
3.  [Requirements](#requirements)
4.  [Installation](#installation)
5.  [The Three Ways to Reach the SEO Manager](#the-three-ways-to-reach-the-seo-manager)
6.  [Rendering Everything: the `@seo` Directive](#rendering-everything-the-seo-directive)
7.  [LaraKit Installer](#larakit-installer)
8.  [Basic SEO](#basic-seo)
9.  [Open Graph](#open-graph)
10. [Twitter Cards](#twitter-cards)
11. [Schema Objects (JSON-LD)](#schema-objects-json-ld)
12. [Article Schema](#article-schema)
13. [Product Schema](#product-schema)
14. [Organization Schema](#organization-schema)
15. [WebSite Schema](#website-schema)
16. [Person Schema](#person-schema)
17. [FAQPage Schema](#faqpage-schema)
18. [Breadcrumb Schema](#breadcrumb-schema)
19. [The Generic Escape Hatch](#the-generic-escape-hatch)
20. [Schema Relationships](#schema-relationships)
21. [Configuration Reference](#configuration-reference)
22. [Full End-to-End Example](#full-end-to-end-example)
23. [Known Limitations](#known-limitations)
24. [Testing](#testing)
25. [Architecture](#architecture)
26. [Design Philosophy](#design-philosophy)
27. [Current Modules](#current-modules)
28. [Roadmap](#roadmap)
29. [Contributing](#contributing)
30. [Author and The Rajat Space](#author-and-the-rajat-space)
31. [License](#license)

---

# What is LaraKit?

LaraKit is a Laravel package intended to collect reusable functionality
that is commonly needed when building Laravel applications.

The first implemented area is SEO.

The current SEO layer covers:

- page titles
- meta descriptions
- meta keywords
- robots metadata
- canonical URLs
- Open Graph metadata
- Twitter Card metadata
- JSON-LD structured data
- Schema.org objects
- schema IDs
- schema references
- schema relationships (automatic and manual)
- schema graphs
- Laravel service-container integration
- a Laravel Artisan installer

The package is being developed as a modular toolkit. Future releases are
planned to add authentication, an admin panel, image optimization, and
other reusable Laravel utilities.

---

# Current Status

## Version `v1.5.0`

The `v1.0.0` release established the initial SEO implementation and was
published to Packagist as:

```text
therajatspace/larakit
```

The `v1.5.0` development line adds the LaraKit module installer.

As of this version, **only the SEO module is actually implemented**. The
installer references three other modules — Authentication, Admin Panel,
and Image Optimization — but selecting any of them currently prints a
message that the module is not available yet rather than pretending it
was installed.

```text
SEO                  → Implemented
Authentication        → Planned
Admin Panel           → Planned
Image Optimization    → Planned
```

---

# Requirements

LaraKit currently requires:

- PHP `^8.3`
- Laravel 13.x / Laravel Illuminate 13.x components

The package currently uses:

- `illuminate/support`
- `illuminate/view`
- `illuminate/console`
- `laravel/prompts`

Development dependencies include:

- PHPUnit 12.5+
- Orchestra Testbench 11.2+

---

# Installation

Install LaraKit through Composer:

```bash
composer require therajatspace/larakit
```

Laravel's package auto-discovery registers `LaraKitServiceProvider`
automatically — there is no manual provider registration step.

On boot, the provider binds the following classes into the container as
**singletons** (one instance per request):

- `SchemaContext`, `SchemaRelationshipResolver`, `SchemaManager`
- `OpenGraphManager`, `TwitterCardManager`, `SchemaConfigurator`
- `SeoManager` (the main object you interact with)

**Why singletons matter:** because each of these lives for the whole
request, you can configure SEO data in a controller and it will still be
present when the Blade view renders — there is no need to pass data
through the view manually.

## Publishing the config file (optional)

```bash
php artisan vendor:publish --tag=larakit-config
```

This produces `config/larakit.php`, which controls default
title/description/robots values and (optionally) a site-wide
Organization and WebSite schema. See [Configuration
Reference](#configuration-reference) for the full breakdown.

---

# The Three Ways to Reach the SEO Manager

All three resolve the exact same singleton instance for the current
request — pick whichever fits your code style.

```php
// 1. Container resolution
$seo = app(\Therajatspace\Larakit\SEO\SeoManager::class);

// 2. Facade (used throughout this README)
use Therajatspace\Larakit\Facades\Seo;
Seo::title('My Page');

// 3. Constructor / method injection
public function show(\Therajatspace\Larakit\SEO\SeoManager $seo)
{
    // ...
}
```

---

# Rendering Everything: the `@seo` Directive

LaraKit provides an `@seo` Blade directive that renders everything you've
configured — basic meta tags, Open Graph, Twitter Cards, and the JSON-LD
schema graph — in one place.

```blade
<!DOCTYPE html>
<html lang="en">
<head>

    @seo

</head>
<body>

    @yield('content')

</body>
</html>
```

The directive compiles to `echo app(SeoManager::class)->render();`. It
must run after your controller has configured the `Seo` facade, which is
naturally the case since controllers execute before views render.

---

# LaraKit Installer

LaraKit provides:

```bash
php artisan larakit:install
```

Running it without flags opens an interactive multiselect prompt (built
with Laravel Prompts) that defaults to SEO:

```text
LaraKit Installation

Which modules do you want to install?

☑ SEO
☐ Authentication
☐ Admin Panel
☐ Image Optimization
```

Typical controls are:

- Up / Down arrows --- move between modules
- Space --- select or deselect a module
- Enter --- confirm

## Installing specific modules with flags

You can skip the interactive menu by using flags directly:

```bash
php artisan larakit:install --seo
php artisan larakit:install --auth
php artisan larakit:install --admin
php artisan larakit:install --image
php artisan larakit:install --seo --auth   # flags can be combined
php artisan larakit:install --all          # runs every check
```

| Flag      | Behavior                                                     |
| --------- | ------------------------------------------------------------ |
| `--seo`   | Prints a confirmation; SEO needs no setup, works immediately |
| `--auth`  | Prints "Authentication module is not available yet."         |
| `--admin` | Prints "Admin Panel module is not available yet."            |
| `--image` | Prints "Image Optimization module is not available yet."     |
| `--all`   | Runs all four checks above                                   |

No files are published and no stubs are copied — the installer is purely
informational for the SEO module today, because SEO functionality is
already available after Composer installation and Laravel package
discovery.

## Installer philosophy

```text
Select modules
      ↓
Determine selected modules
      ↓
Run the corresponding installer
      ↓
Report the result
```

The command is responsible for selection and coordination. Individual
modules can have their own small installer classes when actual
installation work is required.

---

# Basic SEO

This is the foundation layer inside `SeoManager` itself — the plain
`<title>`, `<meta>`, and canonical tags that Open Graph and Twitter Cards
can inherit from.

## Method reference

| Method                             | Produces                    |
| ---------------------------------- | --------------------------- |
| `title(string $title)`             | `<title>` tag               |
| `description(string $description)` | `<meta name="description">` |
| `keywords(string $keywords)`       | `<meta name="keywords">`    |
| `robots(string $robots)`           | `<meta name="robots">`      |
| `canonical(string $url)`           | `<link rel="canonical">`    |

All methods are fluent (return `static`) and unvalidated — any string is
accepted. Values are automatically escaped with `htmlspecialchars(...,
ENT_QUOTES, 'UTF-8')` before printing, so passing raw user input is safe
from XSS.

Every block in `render()` is wrapped in an `if` check — unset fields are
skipped entirely, so you never get an empty `<meta name="description"
content="">`.

## Example — full basic SEO setup

```php
use Therajatspace\Larakit\Facades\Seo;

Seo::title('Understanding Laravel Service Providers')
    ->description('A practical guide to how Laravel service providers work.')
    ->keywords('laravel, service provider, php')
    ->robots('index, follow')
    ->canonical('https://example.com/blog/laravel-service-providers');
```

Output (from `@seo`):

```html
<title>Understanding Laravel Service Providers</title>
<meta
  name="description"
  content="A practical guide to how Laravel service providers work."
/>
<meta name="keywords" content="laravel, service provider, php" />
<meta name="robots" content="index, follow" />
<link
  rel="canonical"
  href="https://example.com/blog/laravel-service-providers"
/>
```

## Example — minimal (only title)

```php
Seo::title('Home');
```

```html
<title>Home</title>
```

## Example — config-driven defaults

`SeoManager`'s constructor reads fallback values from
`config('larakit.seo.defaults')`:

```php
// config/larakit.php
'defaults' => [
    'title'   => 'My Site — Default Title',
    'robots'  => 'index, follow',
],
```

Any page that never calls `Seo::title()` will still render the default
title and robots tag. Calling `Seo::title()` on a specific page simply
overrides it.

> **Gotcha:** only `title`, `description`, and `robots` are read from
> config defaults in the constructor. `keywords` and `canonical` have
> **no** config default support — they must be set per page.

---

# Open Graph

Open Graph controls how a link looks when shared on Facebook, LinkedIn,
Slack, Discord, and WhatsApp. Handled by `OpenGraphManager`, accessed via
`Seo::openGraph()`.

## Method reference

| Method                                                    | Produces / Notes                                               |
| --------------------------------------------------------- | -------------------------------------------------------------- |
| `title(string $title)`                                    | `og:title`                                                     |
| `description(string $description)`                        | `og:description`                                               |
| `type(string $type)`                                      | `og:type` — validated against a whitelist (below)              |
| `url(string $url)`                                        | `og:url`                                                       |
| `image($url, $alt = null, $width = null, $height = null)` | adds an image; callable multiple times                         |
| `getFirstImage()`                                         | returns the first image array, or `null` (feeds Twitter Cards) |

Valid `type()` values: `website`, `article`, `book`, `profile`,
`music.song`, `music.album`, `music.playlist`, `music.radio_station`,
`video.movie`, `video.episode`, `video.tv_show`, `video.other`. Anything
else throws `InvalidArgumentException`.

## Example — fully manual Open Graph

```php
Seo::openGraph()
    ->title('Understanding Laravel Service Providers')
    ->description('A deep dive into how Laravel wires services together.')
    ->type('article')
    ->url('https://example.com/blog/laravel-service-providers')
    ->image('https://example.com/images/laravel-og.jpg',
        alt: 'Laravel logo on a gradient background', width: 1200, height: 630);
```

```html
<meta property="og:title" content="Understanding Laravel Service Providers" />
<meta
  property="og:description"
  content="A deep dive into how Laravel wires services together."
/>
<meta property="og:type" content="article" />
<meta
  property="og:url"
  content="https://example.com/blog/laravel-service-providers"
/>
<meta property="og:image" content="https://example.com/images/laravel-og.jpg" />
<meta property="og:image:alt" content="Laravel logo on a gradient background" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
```

## The inheritance shortcut

`SeoManager::render()` calls `$openGraph->inherit($title, $description,
$canonical)` before rendering, which fills a field **only if it is still
null**. This means basic SEO fields cascade into Open Graph
automatically.

### Example — zero Open Graph calls needed

```php
Seo::title('Understanding Laravel Service Providers')
   ->description('A deep dive into how Laravel wires services together.')
   ->canonical('https://example.com/blog/laravel-service-providers');
// No openGraph() calls at all
```

```html
<title>Understanding Laravel Service Providers</title>
<meta
  name="description"
  content="A deep dive into how Laravel wires services together."
/>
<link
  rel="canonical"
  href="https://example.com/blog/laravel-service-providers"
/>
<meta property="og:title" content="Understanding Laravel Service Providers" />
<meta
  property="og:description"
  content="A deep dive into how Laravel wires services together."
/>
<meta
  property="og:url"
  content="https://example.com/blog/laravel-service-providers"
/>
```

Three basic-SEO calls produced six tags. Note there is no `og:type` or
`og:image` — those have no basic-SEO equivalent, so they must always be
set explicitly.

### Example — explicit value overrides inheritance

```php
Seo::title('Understanding Laravel Service Providers')
   ->description('A deep dive into service providers.');

Seo::openGraph()->title('The Laravel Service Provider Guide Everyone Needs');
```

Because `og:title` was already set explicitly (non-null) before
`inherit()` ran, the inherit call skipped it. Description was left
untouched, so it inherited normally.

### Example — multiple images & validation

```php
Seo::openGraph()
    ->image('https://example.com/images/hero.jpg', width: 1200, height: 630)
    ->image('https://example.com/images/hero-square.jpg', width: 800, height: 800);

Seo::openGraph()->type('slideshow');
// throws InvalidArgumentException: "Invalid Open Graph type: slideshow"

Seo::openGraph()->image('not-a-url');
// throws InvalidArgumentException: "Invalid Open Graph image URL: not-a-url"
```

> **Gotcha:** `getFirstImage()` is not just a convenience getter —
> `SeoManager::render()` uses it to supply Twitter Cards' fallback image.
> The **order** in which you add OG images matters, since only the first
> becomes the Twitter fallback.

---

# Twitter Cards

Controls link previews on X/Twitter. Handled by `TwitterCardManager`,
accessed via `Seo::twitter()`. Structurally the sibling of
`OpenGraphManager`, with the same inheritance pattern.

## Method reference

| Method                             | Produces / Notes                                                              |
| ---------------------------------- | ----------------------------------------------------------------------------- |
| `card(string $card)`               | `twitter:card` — whitelist: `summary`, `summary_large_image`, `app`, `player` |
| `title(string $title)`             | `twitter:title`                                                               |
| `description(string $description)` | `twitter:description`                                                         |
| `image($url, $alt = null)`         | `twitter:image` (+ `:alt`) — only **one** image, unlike Open Graph            |
| `site(string $site)`               | `twitter:site` (publication's @handle)                                        |
| `creator(string $creator)`         | `twitter:creator` (author's @handle)                                          |

## Example — fully manual Twitter Card

```php
Seo::twitter()
    ->card('summary_large_image')
    ->title('Understanding Laravel Service Providers')
    ->description('A deep dive into how Laravel wires services together.')
    ->image('https://example.com/images/twitter-card.jpg', alt: 'Laravel logo')
    ->site('@laravelphp')
    ->creator('@siddharth_dev');
```

```html
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="Understanding Laravel Service Providers" />
<meta
  name="twitter:description"
  content="A deep dive into how Laravel wires services together."
/>
<meta
  name="twitter:image"
  content="https://example.com/images/twitter-card.jpg"
/>
<meta name="twitter:image:alt" content="Laravel logo" />
<meta name="twitter:site" content="@laravelphp" />
<meta name="twitter:creator" content="@siddharth_dev" />
```

## The double inheritance chain

`SeoManager::render()` wires Twitter from **two** sources:

```php
$this->twitter->inherit($this->title, $this->meta['description'] ?? null);
$this->twitter->inheritImage($this->openGraph->getFirstImage());
```

Title and description fall back to basic SEO; the image falls back to
**Open Graph's first image** (basic SEO has no image concept).

### Example — Twitter inherits from title/description/OG image

```php
Seo::title('Understanding Laravel Service Providers')
   ->description('A deep dive into service providers.');

Seo::openGraph()
   ->image('https://example.com/images/hero.jpg', alt: 'Hero image', width: 1200, height: 630)
   ->image('https://example.com/images/hero-square.jpg'); // 2nd image ignored by Twitter
```

```html
<meta name="twitter:title" content="Understanding Laravel Service Providers" />
<meta
  name="twitter:description"
  content="A deep dive into service providers."
/>
<meta name="twitter:image" content="https://example.com/images/hero.jpg" />
<meta name="twitter:image:alt" content="Hero image" />
```

Only the first OG image is used; width/height are silently dropped since
Twitter Cards has no equivalent tag. No `twitter:card` is ever
inherited — it must always be set explicitly.

### Example — full realistic social setup

```php
Seo::title('Understanding Laravel Service Providers')
   ->description('A deep dive into how Laravel wires services together.')
   ->canonical('https://example.com/blog/laravel-service-providers');

Seo::openGraph()
   ->type('article')
   ->image('https://example.com/images/laravel-og.jpg', width: 1200, height: 630);

Seo::twitter()
   ->card('summary_large_image')
   ->site('@laravelphp')
   ->creator('@siddharth_dev');
```

> **Gotcha:** there is no `type()` / `url()` equivalent for Twitter —
> those concepts don't exist in the spec. Forgetting `->card(...)` is the
> most common way to end up with a broken-looking Twitter preview: every
> other tag present, but ignored by the platform without a valid card
> type.

---

# Schema Objects (JSON-LD)

Builds the `<script type="application/ld+json">` block so search engines
understand your page as structured entities. Handled by `SchemaManager`
plus a family of `SchemaObject` subclasses under `src/SEO/Schema/`.

## The base class: `SchemaObject`

Every schema starts with `['@context' => 'https://schema.org']` and
inherits these methods:

| Method                                | Purpose                                                          |
| ------------------------------------- | ---------------------------------------------------------------- |
| `type(string $type)`                  | sets `@type` (subclasses set this in their constructor already)  |
| `name()` / `description()` / `url()`  | common fields                                                    |
| `property(string $key, mixed $value)` | escape hatch — sets any arbitrary field                          |
| `id(string $id)` / `hasId(): bool`    | sets / checks `@id`                                              |
| `reference($id)` / `ref($id)`         | returns `['@id' => $id]` as array, or a `SchemaReference` object |
| `toArray(): array`                    | raw data array                                                   |
| `fromArray(array $data)`              | bulk-assign — see gotcha below                                   |

> **Gotcha:** the base class's `fromArray()` merges keys directly with
> **no validation**. Every subclass (`ArticleSchema`, `ProductSchema`,
> etc.) overrides `fromArray()` to route fields through validated fluent
> setters instead — so validation only applies when using the typed
> subclasses, not the raw generic object.

## Reaching the schema layer

```php
Seo::schema();         // generic SchemaObject — build entirely by hand
Seo::article();        // -> ArticleSchema
Seo::breadcrumbs();    // -> BreadcrumbSchema
Seo::organization();   // -> OrganizationSchema
Seo::website();        // -> WebSiteSchema
Seo::product();        // -> ProductSchema
```

Every specialized method (all except `schema()`) automatically:
instantiates the class, pushes it into the page's shared `SchemaGraph`,
assigns an `@id`, and populates it from the array you pass in.

## Page-scoped vs. site-scoped IDs

| Schema                                    | ID basis                                                 |
| ----------------------------------------- | -------------------------------------------------------- |
| `article()`, `breadcrumbs()`, `product()` | **Current page URL** (page-scoped)                       |
| `organization()`, `website()`             | **Site base URL** from `config('app.url')` (site-scoped) |

IDs take the form `{url}/#{fragment}`, e.g.
`https://example.com/#organization`.

---

# Article Schema

```php
Therajatspace\Larakit\SEO\Schema\ArticleSchema
```

Automatically uses `"@type": "Article"`.

```php
Seo::article([
    'name'          => 'Understanding Laravel Service Providers',
    'headline'      => 'Understanding Laravel Service Providers',
    'description'   => 'A deep dive into how Laravel wires services together.',
    'author'        => 'Siddharth Sharma',
    'datePublished' => '2026-08-17',
    'image'         => 'https://example.com/images/laravel-og.jpg',
]);
```

Output (current URL: `/blog/laravel-service-providers`):

```json
{
  "@context": "https://schema.org",
  "@type": "Article",
  "@id": "https://example.com/blog/laravel-service-providers/#article",
  "name": "Understanding Laravel Service Providers",
  "description": "A deep dive into how Laravel wires services together.",
  "headline": "Understanding Laravel Service Providers",
  "author": { "@type": "Person", "name": "Siddharth Sharma" },
  "datePublished": "2026-08-17",
  "image": "https://example.com/images/laravel-og.jpg"
}
```

`author()` always wraps the name in a `Person` object — there is
currently no way to pass an Organization as author. `datePublished` /
`dateModified` accept only: `Y`, `Y-m`, `Y-m-d`, or full ISO 8601.
Anything else throws.

Manual linking is also available:

```php
$article->publisher('https://example.com/#organization');
$article->isPartOf('https://example.com/#website');
```

(See [Schema Relationships](#schema-relationships) for automatic
linking.)

---

# Product Schema

```php
Therajatspace\Larakit\SEO\Schema\ProductSchema
```

Automatically uses `"@type": "Product"`.

```php
Seo::product([
    'name' => 'LaraKit Pro',
    'description' => 'A Laravel toolkit.',
    'image' => 'https://example.com/images/larakit-pro.png',
    'brand' => 'The Rajat Space',
    'sku' => 'LRK-PRO-001',
    'offers' => [
        'price' => '49.99',
        'priceCurrency' => 'USD',
        'availability' => 'https://schema.org/InStock',
    ],
]);
```

```json
{
  "@type": "Product",
  "@id": "https://example.com/products/larakit-pro/#product",
  "name": "LaraKit Pro",
  "description": "A Laravel toolkit.",
  "image": "https://example.com/images/larakit-pro.png",
  "brand": { "@type": "Brand", "name": "The Rajat Space" },
  "sku": "LRK-PRO-001",
  "offers": {
    "@type": "Offer",
    "price": "49.99",
    "priceCurrency": "USD",
    "availability": "https://schema.org/InStock"
  }
}
```

`image()` and `brand()` validate/normalize their input; `brand` is
represented as a `Brand` object and `offers` is wrapped as an `Offer`
object automatically.

> **Gotcha:** a Product's `@id` is built from the **current request
> URL**, not the `url` value you pass in the data array. These will
> diverge if you build the schema outside of the page it represents
> (e.g. in a queued job).

---

# Organization Schema

```php
Therajatspace\Larakit\SEO\Schema\OrganizationSchema
```

Automatically uses `"@type": "Organization"`.

```php
Seo::organization([
    'name'    => 'The Rajat Space',
    'url'     => 'https://therajatspace.in',
    'logo'    => 'https://therajatspace.in/logo.png',
    'same_as' => ['https://github.com/therajatspace'],
]);
```

Output (with `app.url = https://example.com`):

```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "@id": "https://example.com/#organization",
  "name": "The Rajat Space",
  "url": "https://therajatspace.in",
  "logo": "https://therajatspace.in/logo.png",
  "sameAs": ["https://github.com/therajatspace"]
}
```

Input key `same_as` (snake_case) becomes output key `sameAs` (camelCase,
per Schema.org spec). `logo()` and `sameAs()` validate every URL.

---

# WebSite Schema

```php
Therajatspace\Larakit\SEO\Schema\WebSiteSchema
```

Automatically uses `"@type": "WebSite"`.

```php
Seo::website([
    'name' => 'The Rajat Space',
    'url' => 'https://therajatspace.in',
    'description' => 'Technology and freelancing services.',
]);
```

## Publisher relationship

```php
$website->publisher('https://therajatspace.in/#organization');
```

```json
"publisher": {
    "@id": "https://therajatspace.in/#organization"
}
```

---

# Person Schema

LaraKit provides a dedicated `PersonSchema` for representing people such as authors, developers, creators, employees, speakers, and other individuals.

````php
use Therajatspace\Larakit\Facades\Seo;

Seo::person([
    'name' => 'Siddharth Sharma',
    'givenName' => 'Siddharth',
    'familyName' => 'Sharma',
    'jobTitle' => 'Laravel Developer',
    'email' => 'siddharth@example.com',
    'telephone' => '+91-9876543210',
    'image' => 'https://example.com/images/siddharth.jpg',
    'url' => 'https://example.com/about/siddharth',
    'sameAs' => 'https://github.com/example',
]);
  ```

  ---

  # FAQPage Schema

  LaraKit provides a dedicated `FAQPageSchema` for generating FAQ structured data.

  ## Basic usage

  ```php
  use Therajatspace\Larakit\Facades\Seo;

  Seo::faqPage([
    'name' => 'Frequently Asked Questions',
    'description' => 'Frequently asked questions about LaraKit.',
    'url' => 'https://example.com/faq',
    'questions' => [
      [
        'question' => 'What is LaraKit?',
        'answer' => 'LaraKit is a Laravel SEO toolkit.',
      ],
      [
        'question' => 'Is LaraKit open source?',
        'answer' => 'Yes, LaraKit is open source.',
      ],
    ],
  ]);
  ```


# Breadcrumb Schema

```php
Therajatspace\Larakit\SEO\Schema\BreadcrumbSchema
````

Automatically uses `"@type": "BreadcrumbList"`.

```php
Seo::breadcrumbs()
    ->item('Home', 'https://example.com')
    ->item('Blog', 'https://example.com/blog')
    ->item('Laravel', 'https://example.com/blog/laravel');
```

```json
{
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "https://example.com"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Blog",
      "item": "https://example.com/blog"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "Laravel",
      "item": "https://example.com/blog/laravel"
    }
  ]
}
```

`position` is auto-calculated on each `->item()` call — never supplied
manually. Each URL is validated.

---

# The Generic Escape Hatch

For any Schema.org type without a dedicated class (`Event`, `Recipe`,
`FAQPage`, `Person`, ...), use the base object directly:

```php
Seo::schema()
    ->type('Person')
    ->name('Siddharth Sharma')
    ->property('jobTitle', 'Software Engineer')
    ->property('worksFor', ['@type' => 'Organization', 'name' => 'The Rajat Space']);
```

> **Note:** unlike `article()` / `organization()` / etc., `schema()`
> does **not** auto-assign an `@id`. Call `->id(...)` yourself if it
> needs to be referenceable.

## Rendering into one JSON-LD block

Every schema created via the factory methods is pushed into one shared
`SchemaGraph`. It renders as a single `<script>` tag wrapping all
schemas in one `@graph` array, using `JSON_UNESCAPED_SLASHES |
JSON_UNESCAPED_UNICODE` (so URLs print cleanly, without escaped
slashes).

## Introspection

```php
app(SchemaManager::class)->count();               // int — schemas in the graph
app(SchemaManager::class)->findByType('Article');  // ?SchemaObject — first match, or null
```

---

# Schema Relationships

Turns separate JSON-LD objects into one connected graph, which is what
search engines actually want to see. Handled by
`SchemaRelationshipResolver` (automatic) and `SchemaManager::connect()`
(manual).

## Automatic linking

Every time `SchemaManager::render()` runs, it calls
`$this->relationshipResolver->resolve($this->graph)` first. The resolver
looks through the whole graph for one of each type and, if both schemas
already have an `@id`, wires them together:

| From      | Property    | To             |
| --------- | ----------- | -------------- |
| `WebSite` | `publisher` | `Organization` |
| `Article` | `publisher` | `Organization` |
| `Article` | `isPartOf`  | `WebSite`      |

You don't call anything for this to happen — just create the schemas and
let `@seo` render.

### Example — automatic linking, minimal setup

```php
Seo::organization(['name' => 'The Rajat Space', 'url' => 'https://therajatspace.in']);
Seo::website(['name' => 'The Rajat Space', 'url' => 'https://therajatspace.in']);
Seo::article(['name' => 'Understanding Laravel Service Providers']);
```

Output (excerpt, `@graph` array):

```json
{"@type":"Organization","@id":"https://example.com/#organization"},
{"@type":"WebSite","@id":"https://example.com/#website",
  "publisher": {"@id": "https://example.com/#organization"}},
{"@type":"Article","@id":"https://example.com/blog/.../#article",
  "publisher": {"@id": "https://example.com/#organization"},
  "isPartOf": {"@id": "https://example.com/#website"}}
```

Nobody called `->publisher()` or `->isPartOf()` — all three relationships
were wired automatically.

### Example — manual values are never overwritten

```php
Seo::organization(['name' => 'Org A', 'url' => 'https://a.example']);
$article = Seo::article(['name' => 'My Post']);
$article->publisher('https://some-other-publisher.example/#organization');
```

The resolver checks whether the target property already exists on the
source schema before writing to it. Since `publisher` is already set,
the resolver skips it — your manual link always wins over the automatic
one.

### Example — partial graphs never crash

```php
Seo::article(['name' => 'Solo Post']); // no Organization, no WebSite present
```

Every relationship condition checks that both schemas exist and both
have an `@id` before connecting. If either is missing, that relationship
is silently skipped — no relationships are added, and no error is
thrown.

## Manual linking: `SchemaManager::connect()`

For relationships the automatic resolver doesn't know about (it only
knows the three Organization/WebSite/Article combinations above), use
the general-purpose connector directly:

```php
$schemaManager = app(\Therajatspace\Larakit\SEO\Schema\SchemaManager::class);

$org = Seo::organization(['name' => 'The Rajat Space', 'url' => 'https://therajatspace.in']);
$product = Seo::product(['name' => 'LaraKit Pro']);

$schemaManager->connect($product, 'manufacturer', $org);
```

```json
{
  "@type": "Product",
  "@id": "https://example.com/products/.../#product",
  "name": "LaraKit Pro",
  "manufacturer": { "@id": "https://example.com/#organization" }
}
```

`connect()` accepts any property name string, so it can express any
Schema.org relationship, not just the three built-in ones.

## Config-driven Organization/WebSite: `configureSchemas()`

> **Gotcha — not automatic:** `config/larakit.php` ships with an
> `organization` and `website` section, which reasonably suggests
> filling them in creates those schemas on every page automatically. **It
> does not.** You must explicitly call:

```php
// e.g. in AppServiceProvider::boot()
Seo::configureSchemas(config('larakit.seo'));
```

This checks `config['schema']['auto']` (default `true`) and, unless
disabled, creates Organization/WebSite schemas from config — but only if
each has a non-empty `name`. Calling this once site-wide means every
Article automatically gets linked to your Organization/WebSite via the
resolver above, without repeating the setup per page.

---

# Configuration Reference

`config/larakit.php` in full:

```php
return [
    'seo' => [
        'enabled' => true,
        'defaults' => [
            'title' => null,       // fallback <title> when not set per-page
            'description' => null, // fallback meta description
            'robots' => null,      // fallback robots directive
        ],
        'schema' => [
            'auto' => true,        // used by configureSchemas() — see Schema Relationships
        ],
        'organization' => [
            'name' => null, 'url' => null, 'logo' => null, 'same_as' => [],
        ],
        'website' => [
            'name' => null, 'url' => null, 'description' => null,
        ],
    ],
];
```

The package merges its default configuration into Laravel's
configuration system, so application-specific values can be kept in the
Laravel application's configuration environment rather than hard-coded
inside the package.

> **Remember:** the `organization` / `website` / `schema.auto` keys only
> take effect if you explicitly call
> `Seo::configureSchemas(config('larakit.seo'))` — see [Schema
> Relationships](#schema-relationships).

---

# Full End-to-End Example

```php
// AppServiceProvider::boot() — runs once, site-wide
Seo::configureSchemas(config('larakit.seo'));

// ArticleController::show()
Seo::title('Understanding Laravel Service Providers')
   ->description('A deep dive into how Laravel wires services together.')
   ->canonical('https://example.com/blog/laravel-service-providers');

Seo::openGraph()
   ->type('article')
   ->image('https://example.com/images/laravel-og.jpg', width: 1200, height: 630);

Seo::twitter()
   ->card('summary_large_image')
   ->site('@laravelphp');

Seo::article([
    'headline'      => 'Understanding Laravel Service Providers',
    'author'        => 'Siddharth Sharma',
    'datePublished' => '2026-08-17',
]);

Seo::breadcrumbs()
    ->item('Home', 'https://example.com')
    ->item('Blog', 'https://example.com/blog')
    ->item('Laravel Service Providers', 'https://example.com/blog/laravel-service-providers');
```

One `@seo` directive in the layout now renders: title + meta description

- canonical, full Open Graph (with type and image), a Twitter Card, and
  one JSON-LD `@graph` containing Organization, WebSite, and Article
  (auto-linked to both) plus a Breadcrumb list — from roughly 15 lines of
  page-level code, plus one line of site-wide setup.

## Output (from `@seo`)

> This assumes `config/larakit.php`'s `organization` and `website`
> sections have been filled in (e.g. with `The Rajat Space` /
> `https://therajatspace.in`) — `configureSchemas()` only creates those
> two schemas when a `name` is present, per the
> [`configureSchemas()` gotcha](#schema-relationships) above.

```html
<title>Understanding Laravel Service Providers</title>
<meta
  name="description"
  content="A deep dive into how Laravel wires services together."
/>
<link
  rel="canonical"
  href="https://example.com/blog/laravel-service-providers"
/>
<meta property="og:title" content="Understanding Laravel Service Providers" />
<meta
  property="og:description"
  content="A deep dive into how Laravel wires services together."
/>
<meta property="og:type" content="article" />
<meta
  property="og:url"
  content="https://example.com/blog/laravel-service-providers"
/>
<meta property="og:image" content="https://example.com/images/laravel-og.jpg" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="Understanding Laravel Service Providers" />
<meta
  name="twitter:description"
  content="A deep dive into how Laravel wires services together."
/>
<meta
  name="twitter:image"
  content="https://example.com/images/laravel-og.jpg"
/>
<meta name="twitter:site" content="@laravelphp" />
<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        "@id": "https://example.com/#organization",
        "name": "The Rajat Space",
        "url": "https://therajatspace.in"
      },
      {
        "@type": "WebSite",
        "@id": "https://example.com/#website",
        "name": "The Rajat Space",
        "url": "https://therajatspace.in",
        "publisher": { "@id": "https://example.com/#organization" }
      },
      {
        "@type": "Article",
        "@id": "https://example.com/blog/laravel-service-providers/#article",
        "headline": "Understanding Laravel Service Providers",
        "author": { "@type": "Person", "name": "Siddharth Sharma" },
        "datePublished": "2026-08-17",
        "publisher": { "@id": "https://example.com/#organization" },
        "isPartOf": { "@id": "https://example.com/#website" }
      },
      {
        "@type": "BreadcrumbList",
        "itemListElement": [
          {
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "https://example.com"
          },
          {
            "@type": "ListItem",
            "position": 2,
            "name": "Blog",
            "item": "https://example.com/blog"
          },
          {
            "@type": "ListItem",
            "position": 3,
            "name": "Laravel Service Providers",
            "item": "https://example.com/blog/laravel-service-providers"
          }
        ]
      }
    ]
  }
</script>
```

Notice the `Organization` and `WebSite` nodes were never created on this
page directly — they came from the site-wide `configureSchemas()` call —
and yet `Article.publisher`, `Article.isPartOf`, and `WebSite.publisher`
were all wired automatically by the relationship resolver, with no
`->publisher()` or `->isPartOf()` calls anywhere in the controller.

---

# Known Limitations

- **Only the SEO module is implemented.** Authentication, Admin Panel,
  and Image Optimization are placeholders in the installer with no
  working code behind them.
- **`configureSchemas()` must be called manually.** Config-driven
  Organization/WebSite schemas do not appear automatically just by
  filling in `config/larakit.php`.
- **Article authorship is name-only.** `ArticleSchema::author()` always
  outputs a `Person` type; there is no built-in way to attribute an
  Article to an Organization.
- **Twitter Cards support only a single image**, unlike Open Graph,
  which accepts multiple.
- **Automatic schema relationships cover only three fixed pairings**
  (WebSite→Organization, Article→Organization, Article→WebSite).
  Anything else — e.g. Product→Organization — requires the manual
  `SchemaManager::connect()` call.
- **Product `@id` is based on the current request URL**, not the `url`
  field you supply in the data array — keep these aligned when building
  schemas outside of the page they represent.

---

# Testing

LaraKit is developed with automated tests.

The current test suite covers the SEO layer, schema architecture,
service-container integration, validation utilities, and the installer.

The package currently has more than one hundred automated tests.

Run the complete suite with:

```bash
composer test
```

The installer and Laravel integration are tested using Orchestra
Testbench where framework behavior is required.

The package follows a simple testing philosophy:

```text
Test the behavior LaraKit owns.
Do not duplicate tests for behavior already provided by Laravel.
```

For example, LaraKit tests that the installer selects and dispatches
modules correctly, but does not attempt to re-test Laravel Prompts' own
keyboard navigation implementation.

---

# Architecture

The current architecture is intentionally divided into small areas.

```text
src/
├── Console/
│   ├── Commands/
│   │   └── LaraKitInstall.php
│   └── LaraKitWelcome.php
│
├── Facades/
│   └── Seo.php
│
├── Install/
│   └── SeoInstaller.php
│
├── SEO/
│   ├── OpenGraph/
│   │   └── OpenGraphManager.php
│   │
│   ├── Schema/
│   │   ├── ArticleSchema.php
│   │   ├── BreadcrumbSchema.php
│   │   ├── OrganizationSchema.php
│   │   ├── ProductSchema.php
│   │   ├── SchemaConfigurator.php
│   │   ├── SchemaContext.php
│   │   ├── SchemaGraph.php
│   │   ├── SchemaIdGenerator.php
│   │   ├── SchemaManager.php
│   │   ├── SchemaObject.php
│   │   ├── SchemaReference.php
│   │   ├── SchemaRelationshipResolver.php
│   │   └── WebSiteSchema.php
│   │
│   ├── Support/
│   │   ├── DateValidator.php
│   │   └── UrlValidator.php
│   │
│   ├── Twitter/
│   │   └── TwitterCardManager.php
│   │
│   └── SeoManager.php
│
└── LaraKitServiceProvider.php
```

---

# Design Philosophy

LaraKit is intentionally not trying to replace Laravel.

The package uses Laravel's existing mechanisms whenever they are
appropriate:

- Service Container
- Service Provider
- Blade
- Artisan Commands
- Laravel Prompts
- Laravel configuration
- Composer package discovery

The package adds reusable behavior on top of those mechanisms.

## Keep the code understandable

A major design goal is that a Laravel developer should be able to open
the source code and understand how the package works.

The package therefore favors:

- small classes
- normal PHP objects
- fluent methods
- explicit dependencies
- simple service-container registration
- focused responsibilities
- tests around behavior
- minimal magic

Abstraction is introduced when there is a real repeated problem, not
merely because an abstraction looks architecturally impressive.

---

# Current Modules

## SEO

Status:

```text
Implemented
```

Includes:

- meta information
- Open Graph
- Twitter Cards
- JSON-LD
- Schema.org objects
- graph relationships
- schema IDs
- references
- Laravel integration

## Authentication

Status:

```text
Planned
```

The planned authentication system will cover roles and permissions and
is being designed to support both:

1.  Different login pages for different roles.
2.  A common login page capable of handling different roles.

The authentication architecture will be designed before implementation
so that the package remains understandable and flexible.

## Admin Panel

Status:

```text
Planned
```

The admin panel will be developed after the authentication foundation.

## Image Optimization

Status:

```text
Planned
```

The image optimization module will be designed as a separate LaraKit
module rather than coupling it to the SEO system.

---

# Roadmap

```text
v1.0.0
  │
  └── Initial SEO release
          │
v1.5.0
  │
  └── Modular Artisan installer
          │
          ├── SEO installer
          │
          └── framework for future modules
          │
Future
  │
  ├── Authentication
  ├── Roles
  ├── Permissions
  ├── Admin Panel
  ├── Image Optimization
  └── Additional reusable Laravel utilities
```

Future releases may introduce additional modules and improve existing
SEO capabilities.

---

# Contributing

Contributions, bug reports, suggestions, and improvements are welcome.

Before contributing, please consider:

1.  Keep changes focused.
2.  Follow the existing PHP/Laravel style.
3.  Prefer simple solutions.
4.  Add tests for new behavior.
5.  Run the complete test suite before submitting changes.

Run:

```bash
composer validate
composer test
```

A contribution should ideally leave the project with a clean working
tree and a passing test suite.

---

# Author and The Rajat Space

## Siddharth Sharma

LaraKit is created and maintained by **Siddharth Sharma**.

Email:

```text
siddharthsharmaofficial2@gmail.com
```

## The Rajat Space

LaraKit is being developed under the freelancing service brand **The
Rajat Space**.

Email:

```text
contact@therajatspace.in
```

Website:

```text
https://therajatspace.in
```

GitHub organization/account:

```text
therajatspace
```

LaraKit repository:

```text
https://github.com/therajatspace/Larakit
```

Documentation:

```text
https://therajatspace.in/larakit
```

---

# License

LaraKit is open-source software licensed under the MIT License.

See the `LICENSE` file for the complete license text.

---

**LaraKit — Build smarter. Optimize better. Ship faster.**
