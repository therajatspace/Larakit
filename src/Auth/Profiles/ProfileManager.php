<?php

namespace Therajatspace\Larakit\Auth\Profiles;

use InvalidArgumentException;

class ProfileManager
{
    /**
     * @var array<string, AuthProfile>
     */
    protected array $profiles = [];

    public function __construct(array $profiles = [])
    {
        foreach ($profiles as $name => $configuration) {
            $this->add($name, $configuration);
        }
    }

    public function add(
        string $name,
        array $configuration = []
    ): AuthProfile {
        if (trim($name) === '') {
            throw new InvalidArgumentException(
                'Authentication profile name cannot be empty.'
            );
        }

        if ($this->has($name)) {
            throw new InvalidArgumentException(
                "Authentication profile [{$name}] is already registered."
            );
        }

        $profile = new AuthProfile(
            $name,
            $configuration
        );

        $this->profiles[$name] = $profile;

        return $profile;
    }

    /**
     * @return array<string, AuthProfile>
     */
    public function all(): array
    {
        return $this->profiles;
    }

    public function find(string $name): ?AuthProfile
    {
        return $this->profiles[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return isset($this->profiles[$name]);
    }
}