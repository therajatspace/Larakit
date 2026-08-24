<?php

namespace Therajatspace\Larakit\Auth\Contracts;

interface PasswordConfirmationServiceContract
{
    public function confirm(
        string $password
    ): void;
}
