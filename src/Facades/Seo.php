<?php

namespace Therajatspace\Larakit\Facades;

use Illuminate\Support\Facades\Facade;

class Seo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Therajatspace\Larakit\SEO\SeoManager::class;
    }
}