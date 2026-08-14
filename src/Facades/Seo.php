<?php

namespace Sidd2604\Larakit\Facades;

use Illuminate\Support\Facades\Facade;

class Seo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sidd2604\Larakit\SEO\SeoManager::class;
    }
}