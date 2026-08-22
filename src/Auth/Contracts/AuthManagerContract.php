<?php

namespace Therajatspace\Larakit\Auth\Contracts;

interface AuthManagerContract
{
    public function enabled(): bool;

    public function routeMode(): string;
}