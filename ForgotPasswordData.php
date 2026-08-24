<?php

namespace Therajatspace\Larakit\Auth\Password;

final class ForgotPasswordData
{
    public function __construct(
        public readonly string $email,
    ) {
    }

    public static function make(
        string $email
    ): self {
        return new self(
            email: mb_strtolower(trim($email)),
        );
    }
}