<?php

namespace Therajatspace\Larakit\Auth\Password;

final class ResetPasswordData
{
    public function __construct(
        public readonly string $token,
        public readonly string $email,
        public readonly string $password,
        public readonly string $passwordConfirmation,
    ) {
    }

    public static function make(
        string $token,
        string $email,
        string $password,
        string $passwordConfirmation,
    ): self {
        return new self(
            token: trim($token),
            email: mb_strtolower(trim($email)),
            password: $password,
            passwordConfirmation: $passwordConfirmation,
        );
    }
}
