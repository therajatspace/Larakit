<?php

namespace Therajatspace\Larakit\Auth\Contracts;

interface AuthorizationManagerContract
{
    public function enabled(): bool;

    public function guard(): string;

    public function userModel(): string;

    public function supportsRoles(): bool;

    public function ensureSupported(): void;
}