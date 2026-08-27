<?php

namespace Therajatspace\Larakit\Admin\Contracts;

interface AdminManagerContract
{
    public function enabled(): bool;

    public function routePrefix(): string;

    /**
     * @return array<int, string>
     */
    public function middleware(): array;

    public function moduleEnabled(string $module): bool;
}