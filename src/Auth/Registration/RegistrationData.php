<?php

namespace Therajatspace\Larakit\Auth\Registration;

final class RegistrationData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $profile = null,
    ) {
    }

    /**
     * Create registration data from trusted application input.
     *
     * This class deliberately accepts only known registration fields.
     */
    public static function make(
        string $name,
        string $email,
        string $password,
        ?string $profile = null,
    ): self {
        return new self(
            name: trim($name),
            email: mb_strtolower(trim($email)),
            password: $password,
            profile: $profile !== null
            ? trim($profile)
            : null,
        );
    }
}