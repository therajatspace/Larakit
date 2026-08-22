<?php

namespace Therajatspace\Larakit\Auth\Contracts;

interface AuthProfileContract
{
    public function name(): string;

    public function role(): ?string;

    public function loginEnabled(): bool;

    public function registrationEnabled(): bool;
}