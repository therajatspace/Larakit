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

1.  [What is LaraKit?](#what-is-larikit)
2.  [Current Status](#current-status)
3.  [Requirements](#requirements)
4.  [Installation](#installation)
5.  [LaraKit Installer](#larakit-installer)
6.  [Basic SEO](#basic-seo)
7.  [SEO Manager](#seo-manager)
8.  [Facade](#facade)
9.  [Blade Directive](#blade-directive)
10. [Open Graph](#open-graph)
11. [Twitter Cards](#twitter-cards)
12. [JSON-LD and Schema.org](#json-ld-and-schemaorg)
13. [Schema Objects](#schema-objects)
14. [SchemaObject Base API](#schemaobject-base-api)
15. [Article Schema](#article-schema)
16. [Product Schema](#product-schema)
17. [Organization Schema](#organization-schema)
18. [WebSite Schema](#website-schema)
19. [Breadcrumb Schema](#breadcrumb-schema)
20. [Creating Schemas from Arrays](#creating-schemas-from-arrays)
21. [Schema IDs](#schema-ids)
22. [Schema References](#schema-references)
23. [Schema Graph](#schema-graph)
24. [Schema Relationships](#schema-relationships)
25. [Schema Relationship Resolution](#schema-relationship-resolution)
26. [Schema Context](#schema-context)
27. [Rendering JSON-LD](#rendering-json-ld)
28. [Laravel Service Container
    Integration](#laravel-service-container-integration)
29. [Configuration](#configuration)
30. [Testing](#testing)
31. [Architecture](#architecture)
32. [Design Philosophy](#design-philosophy)
33. [Current Modules](#current-modules)
34. [Roadmap](#roadmap)
35. [Contributing](#contributing)
36. [Author and The Rajat Space](#author-and-the-rajat-space)
37. [License](#license)

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
- schema relationships
- schema graphs
- automatic relationship resolution
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

The installer currently understands these modules:

```text
SEO
Authentication
Admin Panel
Image Optimization
```

SEO is implemented.

Authentication, Admin Panel, and Image Optimization are planned modules
and are currently reported as unavailable by the installer.

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

Because LaraKit registers its Laravel service provider through
Composer's Laravel package discovery, no manual provider registration is
normally required.

After installation, LaraKit can be accessed through Laravel's service
container, its facade, Blade integration, and the Artisan installer.

---

# LaraKit Installer

Starting with the installer work introduced after the initial SEO
release, LaraKit provides:

```bash
php artisan larakit:install
```

The installer uses Laravel Prompts for interactive module selection.

The interactive selector allows multiple modules to be selected with the
keyboard:

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

SEO is selected by default.

## Installing a specific module

You can skip the interactive menu by using flags.

### SEO

```bash
php artisan larakit:install --seo
```

### Authentication

```bash
php artisan larakit:install --auth
```

At the current stage this reports that Authentication is not available
yet.

### Admin Panel

```bash
php artisan larakit:install --admin
```

### Image Optimization

```bash
php artisan larakit:install --image
```

### Multiple modules

Flags can be combined:

```bash
php artisan larakit:install --seo --auth
```

### All modules

```bash
php artisan larakit:install --all
```

The `--all` option currently selects all known LaraKit modules. Modules
that are not yet implemented are reported as unavailable rather than
pretending that they were installed.

## Installer philosophy

The installer is deliberately designed around a simple flow:

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

The SEO installer currently does not need to publish migrations or files
because the SEO functionality is already available after Composer
installation and Laravel package discovery.

---

# Basic SEO

The SEO layer is centered around `SeoManager`.

The manager provides a fluent interface for building the SEO information
that should eventually be rendered into the page `<head>`.

A typical flow is:

```php
$seo = app(\Therajatspace\Larakit\SEO\SeoManager::class);

$seo
    ->title('My Laravel Website')
    ->description('A description of my website.')
    ->canonical('https://example.com')
    ->render();
```

The exact combination of properties can be changed for every page.

The general philosophy is:

```text
Configure SEO
      ↓
Build metadata
      ↓
Build social metadata
      ↓
Build structured data
      ↓
Render once
```

---

# SEO Manager

The main class is:

```php
Therajatspace\Larakit\SEO\SeoManager
```

It coordinates the major SEO systems.

Conceptually:

```text
SeoManager
    ├── Basic SEO metadata
    ├── OpenGraphManager
    ├── TwitterCardManager
    └── SchemaManager
```

This means an application can configure several SEO layers through one
manager and then render the result.

For example:

```php
$seo = app(\Therajatspace\Larakit\SEO\SeoManager::class);

$seo
    ->title('LaraKit')
    ->description('A Laravel toolkit.')
    ->render();
```

The rendered output can contain:

```html
<title>LaraKit</title> <meta name="description" content="A Laravel toolkit." />
```

along with the configured social metadata and JSON-LD output.

---

# Facade

LaraKit provides an SEO facade:

```php
Therajatspace\Larakit\Facades\Seo
```

This allows SEO configuration without manually resolving `SeoManager`
from the service container.

For example:

```php
use Therajatspace\Larakit\Facades\Seo;

Seo::title('LaraKit');
```

The facade resolves the registered `SeoManager` instance from Laravel's
container.

This is useful when you prefer Laravel's familiar facade syntax.

If you prefer explicit dependency resolution, use:

```php
app(\Therajatspace\Larakit\SEO\SeoManager::class)
```

---

# Blade Directive

LaraKit also provides an `@seo` Blade directive.

Inside a Blade layout, you can use:

```blade
<head>

    @seo

</head>
```

The directive resolves the package's `SeoManager` from the Laravel
service container and renders the configured SEO output.

A common layout structure can therefore be:

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

Then individual controllers, services, view composers, or other
application code can configure the SEO manager before the view is
rendered.

---

# Open Graph

LaraKit contains an Open Graph manager:

```php
Therajatspace\Larakit\SEO\OpenGraph\OpenGraphManager
```

Open Graph metadata is used by platforms such as social networks and
messaging applications to understand how a URL should be represented
when shared.

Typical Open Graph information includes:

- title
- description
- URL
- image
- content type
- site information

The Open Graph system is kept separate from the main SEO manager so that
social metadata has its own focused responsibility.

The normal usage pattern is to configure Open Graph information through
the main SEO manager and render it together with the other SEO output.

---

# Twitter Cards

LaraKit contains a Twitter Card manager:

```php
Therajatspace\Larakit\SEO\Twitter\TwitterCardManager
```

Twitter/X cards provide metadata that controls how shared URLs can be
represented on supported social platforms.

The Twitter system is separated from Open Graph because the two systems
have different metadata conventions.

The manager is registered with Laravel's service container and
coordinated by `SeoManager`.

---

# JSON-LD and Schema.org

One of the major parts of LaraKit is JSON-LD structured data.

JSON-LD allows a web page to describe its entities and their
relationships in a machine-readable format.

LaraKit represents JSON-LD data using PHP objects rather than forcing
developers to manually construct large associative arrays.

The basic concept is:

```text
PHP Schema Object
       ↓
toArray()
       ↓
JSON-LD data
       ↓
SchemaManager
       ↓
JSON-LD <script>
```

A schema object is a normal PHP object with fluent methods.

For example:

```php
$organization = new OrganizationSchema();

$organization
    ->name('LaraKit')
    ->url('https://example.com')
    ->logo('https://example.com/logo.png');
```

The resulting array can be inspected with:

```php
$data = $organization->toArray();
```

and will contain Schema.org-compatible JSON-LD data.

---

# Schema Objects

The current schema objects include:

```text
SchemaObject
├── ArticleSchema
├── BreadcrumbSchema
├── OrganizationSchema
├── ProductSchema
└── WebSiteSchema
```

The base class provides common functionality while specialized classes
provide schema-specific methods.

This gives LaraKit two important advantages:

1.  Common properties do not need to be reimplemented in every schema.
2.  Specialized schemas can expose methods appropriate to their
    Schema.org type.

---

# SchemaObject Base API

The base class is:

```php
Therajatspace\Larakit\SEO\Schema\SchemaObject
```

It starts with:

```json
{
  "@context": "https://schema.org"
}
```

## `type()`

Sets the Schema.org type:

```php
$schema->type('Thing');
```

## `name()`

Sets the name:

```php
$schema->name('LaraKit');
```

## `description()`

Sets the description:

```php
$schema->description('Laravel SEO toolkit');
```

## `url()`

Sets the canonical URL of the entity:

```php
$schema->url('https://example.com');
```

## `property()`

Allows an arbitrary property to be added:

```php
$schema->property(
    'customProperty',
    'custom value'
);
```

This is important because Schema.org is much larger than the small set
of convenience methods currently provided by LaraKit.

Instead of requiring a dedicated PHP method for every possible
Schema.org property, developers can use `property()` for additional
data.

## `id()`

Sets the JSON-LD `@id`:

```php
$schema->id('https://example.com/#organization');
```

## `hasId()`

Checks whether the object already contains an `@id`:

```php
if ($schema->hasId()) {
    // The schema has an @id.
}
```

## `reference()`

Creates a simple JSON-LD reference:

```php
$reference = $schema->reference(
    'https://example.com/#organization'
);
```

The result is:

```php
[
    '@id' => 'https://example.com/#organization',
]
```

## `ref()`

Creates a `SchemaReference` object:

```php
$reference = $schema->ref(
    'https://example.com/#organization'
);
```

This is useful when relationships between schemas are being constructed.

## `fromArray()`

Schema data can also be populated from an array:

```php
$schema->fromArray([
    'name' => 'LaraKit',
    'description' => 'Laravel SEO toolkit',
    'url' => 'https://example.com',
]);
```

## `toArray()`

Returns the final schema representation:

```php
$data = $schema->toArray();
```

The result is a normal PHP array and can therefore be inspected, tested,
transformed, or encoded as JSON.

---

# Article Schema

The article schema class is:

```php
Therajatspace\Larakit\SEO\Schema\ArticleSchema
```

It automatically uses:

```json
"@type": "Article"
```

Example:

```php
use Therajatspace\Larakit\SEO\Schema\ArticleSchema;

$article = new ArticleSchema();

$article
    ->name('Understanding Laravel')
    ->headline('Understanding Laravel')
    ->description('A guide to Laravel.')
    ->url('https://example.com/articles/laravel')
    ->author('Siddharth Sharma')
    ->datePublished('2026-08-17')
    ->dateModified('2026-08-17')
    ->image('https://example.com/images/laravel.jpg');
```

## Article-specific methods

### `headline()`

```php
$article->headline('Understanding Laravel');
```

### `author()`

```php
$article->author('Siddharth Sharma');
```

The author is represented as a Person:

```json
"author": {
    "@type": "Person",
    "name": "Siddharth Sharma"
}
```

### `datePublished()`

```php
$article->datePublished('2026-08-17');
```

The date is validated before being stored.

### `dateModified()`

```php
$article->dateModified('2026-08-17');
```

The date is validated before being stored.

### `image()`

```php
$article->image(
    'https://example.com/images/article.jpg'
);
```

The URL is validated before being stored.

## Publisher relationship

An article can reference a publisher by schema ID:

```php
$article->publisher(
    'https://example.com/#organization'
);
```

This produces:

```json
"publisher": {
    "@id": "https://example.com/#organization"
}
```

## Website relationship

An article can also reference the website it belongs to:

```php
$article->isPartOf(
    'https://example.com/#website'
);
```

---

# Product Schema

The product schema class is:

```php
Therajatspace\Larakit\SEO\Schema\ProductSchema
```

It automatically uses:

```json
"@type": "Product"
```

Example:

```php
use Therajatspace\Larakit\SEO\Schema\ProductSchema;

$product = new ProductSchema();

$product
    ->name('LaraKit Pro')
    ->description('A Laravel toolkit.')
    ->url('https://example.com/products/larakit-pro')
    ->image('https://example.com/images/larakit-pro.png')
    ->brand('The Rajat Space')
    ->sku('LRK-PRO-001');
```

## Product-specific methods

### `image()`

```php
$product->image(
    'https://example.com/images/product.jpg'
);
```

The URL is validated.

### `brand()`

```php
$product->brand('The Rajat Space');
```

The output represents the brand as:

```json
"brand": {
    "@type": "Brand",
    "name": "The Rajat Space"
}
```

### `sku()`

```php
$product->sku('PRODUCT-001');
```

### `offers()`

Offers can be supplied as an array:

```php
$product->offers([
    'price' => '49.99',
    'priceCurrency' => 'USD',
    'availability' => 'https://schema.org/InStock',
]);
```

The package adds:

```json
"@type": "Offer"
```

to the offer structure.

---

# Organization Schema

The organization schema class is:

```php
Therajatspace\Larakit\SEO\Schema\OrganizationSchema
```

It automatically uses:

```json
"@type": "Organization"
```

Example:

```php
use Therajatspace\Larakit\SEO\Schema\OrganizationSchema;

$organization = new OrganizationSchema();

$organization
    ->name('The Rajat Space')
    ->url('https://therajatspace.in')
    ->logo('https://therajatspace.in/logo.png')
    ->sameAs([
        'https://github.com/therajatspace',
    ]);
```

## `logo()`

```php
$organization->logo(
    'https://example.com/logo.png'
);
```

The URL is validated.

## `sameAs()`

```php
$organization->sameAs([
    'https://github.com/therajatspace',
    'https://linkedin.com/company/example',
]);
```

Every supplied URL is validated.

The resulting JSON-LD contains:

```json
"sameAs": [
    "https://github.com/therajatspace"
]
```

---

# WebSite Schema

The website schema class is:

```php
Therajatspace\Larakit\SEO\Schema\WebSiteSchema
```

It automatically uses:

```json
"@type": "WebSite"
```

Example:

```php
use Therajatspace\Larakit\SEO\Schema\WebSiteSchema;

$website = new WebSiteSchema();

$website
    ->name('The Rajat Space')
    ->url('https://therajatspace.in')
    ->description('A freelancing and technology service.');
```

## Publisher relationship

A website can reference its publisher:

```php
$website->publisher(
    'https://therajatspace.in/#organization'
);
```

The resulting structure is:

```json
"publisher": {
    "@id": "https://therajatspace.in/#organization"
}
```

---

# Breadcrumb Schema

The breadcrumb schema class is:

```php
Therajatspace\Larakit\SEO\Schema\BreadcrumbSchema
```

It automatically uses:

```json
"@type": "BreadcrumbList"
```

Breadcrumb items can be added fluently:

```php
use Therajatspace\Larakit\SEO\Schema\BreadcrumbSchema;

$breadcrumbs = new BreadcrumbSchema();

$breadcrumbs
    ->item('Home', 'https://example.com')
    ->item('Blog', 'https://example.com/blog')
    ->item(
        'Laravel',
        'https://example.com/blog/laravel'
    );
```

The resulting `itemListElement` contains automatically generated
positions:

```json
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
```

The important part is that developers do not have to manually calculate
the positions.

---

# Creating Schemas from Arrays

Schema objects can be created from structured arrays through
`SchemaManager`.

For example:

```php
$organization = $schemaManager->organization([
    'name' => 'LaraKit',
    'url' => 'https://example.com',
    'logo' => 'https://example.com/logo.png',
    'same_as' => [
        'https://github.com/example',
    ],
]);
```

The manager creates the correct schema object and populates it from the
supplied data.

This is useful when schema configuration comes from:

- database records
- configuration files
- controllers
- CMS data
- application services

The schema-specific `fromArray()` methods convert supported fields
through their corresponding methods, allowing validation and
normalization to remain inside the schema classes.

---

# Schema IDs

LaraKit supports stable schema identifiers using JSON-LD `@id`.

A schema ID allows one entity to be referenced from another schema
without duplicating the entire entity.

For example:

```text
https://example.com/#organization
```

can represent the site's organization.

Another schema can then reference it:

```json
"publisher": {
    "@id": "https://example.com/#organization"
}
```

This is preferable to repeatedly embedding a complete Organization
object when the same organization is already represented elsewhere in
the graph.

---

# Schema Context

The schema context is represented by:

```php
Therajatspace\Larakit\SEO\Schema\SchemaContext
```

It stores:

- the base URL
- the current page URL

Example:

```php
$context = new SchemaContext(
    'https://example.com',
    'https://example.com/blog/laravel'
);
```

The context can generate IDs based on the site's base URL or the current
page URL.

For example:

```text
base URL:
https://example.com

organization:
https://example.com/#organization
```

and:

```text
current URL:
https://example.com/blog/laravel

article:
https://example.com/blog/laravel/#article
```

The context centralizes URL and ID generation instead of making every
schema manually construct these strings.

---

# Schema References

LaraKit has a small `SchemaReference` value object for representing
references between entities.

Conceptually:

```text
Organization
     ↑
     │ @id
     │
Article.publisher
```

Instead of embedding:

```json
"publisher": {
    "@type": "Organization",
    "name": "The Rajat Space",
    "url": "https://therajatspace.in"
}
```

another schema can simply refer to:

```json
"publisher": {
    "@id": "https://therajatspace.in/#organization"
}
```

This keeps the JSON-LD graph concise and makes relationships explicit.

---

# Schema Graph

LaraKit contains:

```php
Therajatspace\Larakit\SEO\Schema\SchemaGraph
```

The graph is the conceptual layer that allows multiple schema entities
to exist together as one JSON-LD graph.

Instead of thinking of structured data as unrelated JSON objects:

```text
Organization
Website
Article
Breadcrumb
Product
```

LaraKit can treat them as related nodes:

```text
                 Organization
                 /          \
                /            \
          publisher        publisher
              /                \
          WebSite             Article
                                 |
                              isPartOf
                                 |
                              WebSite
```

This is particularly useful for larger pages where several entities
describe the same website and need to reference each other.

The graph approach is one of the reasons LaraKit has separate concepts
for:

- schema objects
- IDs
- references
- relationships
- relationship resolution
- graph rendering

---

# Schema Relationships

A schema relationship is a connection from one schema entity to another.

Examples include:

```text
Article → publisher → Organization
Article → isPartOf → WebSite
WebSite → publisher → Organization
```

The relationship is normally represented using an `@id` reference.

For example:

```php
$article->publisher(
    'https://example.com/#organization'
);
```

creates:

```json
"publisher": {
    "@id": "https://example.com/#organization"
}
```

This allows the same Organization node to be reused by several schemas.

---

# Automatic Schema Relationship Resolution

LaraKit also contains:

```php
Therajatspace\Larakit\SEO\Schema\SchemaRelationshipResolver
```

Its purpose is to resolve relationships between schema objects when
building the final graph.

The idea is:

```text
Schema Objects
      ↓
Identify IDs
      ↓
Identify references
      ↓
Resolve relationships
      ↓
Build coherent graph
      ↓
Render JSON-LD
```

This means developers can work with normal schema objects and references
without manually assembling a giant `@graph` array.

The relationship system is intentionally based on ordinary PHP objects
and arrays rather than a separate database-style graph engine.

---

# Writing JSON-LD with LaraKit

The preferred way to write JSON-LD is to use schema objects rather than
manually constructing the final JSON.

For example, instead of:

```php
$data = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => 'My Article',
    'author' => [
        '@type' => 'Person',
        'name' => 'Siddharth Sharma',
    ],
];
```

you can write:

```php
$article = new ArticleSchema();

$article
    ->headline('My Article')
    ->author('Siddharth Sharma');
```

This gives the package a place to perform validation and to evolve the
schema architecture independently of application code.

The final structure can still be inspected:

```php
$data = $article->toArray();
```

So LaraKit does not hide the JSON-LD representation from the developer.

---

# A Complete Schema Example

A common website can have an Organization, Website, and Article.

Conceptually:

```text
Organization
     │
     ├── Website.publisher
     │
     └── Article.publisher

Website
     ↑
     │ isPartOf
     │
Article
```

The individual schemas can be created like this:

```php
$organization = $schemaManager->organization([
    'name' => 'The Rajat Space',
    'url' => 'https://therajatspace.in',
    'logo' => 'https://therajatspace.in/logo.png',
]);

$website = $schemaManager->website([
    'name' => 'The Rajat Space',
    'url' => 'https://therajatspace.in',
    'description' => 'Technology and freelancing services.',
]);

$article = $schemaManager->article([
    'name' => 'Understanding Laravel',
    'headline' => 'Understanding Laravel',
    'url' => 'https://therajatspace.in/blog/laravel',
    'author' => 'Siddharth Sharma',
    'datePublished' => '2026-08-17',
]);
```

Relationships can then reference the appropriate schema IDs.

The important design principle is that the developer works with
**entities and relationships**, while LaraKit handles the JSON-LD
representation.

---

# Rendering JSON-LD

Once schemas are registered with `SchemaManager`, the manager can render
them as JSON-LD.

The output follows the standard structure:

```html
<script type="application/ld+json">
  {
      "@context": "https://schema.org",
      "@graph": [
          ...
      ]
  }
</script>
```

The graph may contain:

- Organization
- WebSite
- Article
- Product
- BreadcrumbList
- other supported schema objects

The schema objects themselves remain normal PHP objects until rendering.

This makes them easy to unit test.

For example:

```php
$data = $organization->toArray();

$this->assertSame(
    'Organization',
    $data['@type']
);
```

---

# Laravel Service Container Integration

LaraKit registers its core managers with Laravel's service container.

The main services include:

```text
SeoManager
OpenGraphManager
TwitterCardManager
SchemaManager
SchemaConfigurator
SchemaRelationshipResolver
SchemaContext
```

This allows Laravel applications to resolve them normally:

```php
$seo = app(
    \Therajatspace\Larakit\SEO\SeoManager::class
);
```

or:

```php
$schema = app(
    \Therajatspace\Larakit\SEO\Schema\SchemaManager::class
);
```

The service provider is:

```php
Therajatspace\Larakit\LaraKitServiceProvider
```

Laravel discovers this provider through the `extra.laravel.providers`
entry in `composer.json`.

---

# Configuration

LaraKit provides:

```text
config/larakit.php
```

The package merges its default configuration into Laravel's
configuration system.

This means application-specific configuration can be kept in the Laravel
application's configuration environment rather than hard-coded inside
the package.

The SEO manager also receives the configured SEO defaults from:

```php
config('larakit.seo.defaults', [])
```

This allows common defaults to be centralized.

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

The general roadmap is:

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

# Quick Reference

## Install

```bash
composer require therajatspace/larakit
```

## Run the installer

```bash
php artisan larakit:install
```

## Install selected modules through flags

```bash
php artisan larakit:install --seo
```

```bash
php artisan larakit:install --auth
```

```bash
php artisan larakit:install --admin
```

```bash
php artisan larakit:install --image
```

```bash
php artisan larakit:install --all
```

## Resolve SEO manager

```php
$seo = app(
    \Therajatspace\Larakit\SEO\SeoManager::class
);
```

## Use the facade

```php
use Therajatspace\Larakit\Facades\Seo;

Seo::title('My Page');
```

## Use the Blade directive

```blade
@seo
```

## Create a schema

```php
$organization = new \Therajatspace\Larakit\SEO\Schema\OrganizationSchema();

$organization
    ->name('LaraKit')
    ->url('https://example.com')
    ->logo('https://example.com/logo.png');
```

## Inspect a schema

```php
$data = $organization->toArray();
```

## Create a breadcrumb

```php
$breadcrumbs
    ->item('Home', 'https://example.com')
    ->item('Blog', 'https://example.com/blog')
    ->item('Laravel', 'https://example.com/blog/laravel');
```

## Run tests

```bash
composer test
```

---

## LaraKit

**Build smarter. Optimize better. Ship faster.**

### Author

**Siddharth Sharma**

Email:
`siddharthsharmaofficial2@gmail.com`

### The Rajat Space

LaraKit is developed under **The Rajat Space**, a freelancing service.

Email:
`contact@therajatspace.in`

Website:
`https://therajatspace.in`

GitHub:
`https://github.com/therajatspace`

LaraKit Documentation:
`https://therajatspace.in/larakit`
