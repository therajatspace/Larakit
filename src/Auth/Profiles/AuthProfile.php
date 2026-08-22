<?php

namespace Therajatspace\Larakit\Auth\Profiles;

use Therajatspace\Larakit\Auth\Contracts\AuthProfileContract;
use InvalidArgumentException;

class AuthProfile implements AuthProfileContract
{
    protected string $name;

    protected ?string $role;

    protected bool $login;

    protected bool $registration;

    public function __construct(
        string $name,
        array $configuration = []
    ) {
        $this->validateName($name);

        $this->name = $name;

        $this->role = $configuration['role'] ?? null;

        $this->login = $configuration['login'] ?? true;

        $this->registration = $configuration['registration'] ?? false;

        $this->validateConfiguration();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function role(): ?string
    {
        return $this->role;
    }

    public function loginEnabled(): bool
    {
        return $this->login;
    }

    public function registrationEnabled(): bool
    {
        return $this->registration;
    }

    protected function validateName(string $name): void
    {
        if (trim($name) === '') {
            throw new InvalidArgumentException(
                'Authentication profile name cannot be empty.'
            );
        }
    }

    protected function validateConfiguration(): void
    {
        if ($this->role !== null && trim($this->role) === '') {
            throw new InvalidArgumentException(
                "Authentication profile [{$this->name}] has an invalid role."
            );
        }
    }
}