<?php

namespace Therajatspace\Larakit\Auth\Login;

final class LoginData
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly bool $remember = false,
    ) {
    }

    public static function make(
        string $email,
        string $password,
        bool $remember = false,
    ): self {
        return new self(
            email: $email,
            password: $password,
            remember: $remember,
        );
    }
}