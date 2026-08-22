<?php

namespace Therajatspace\Larakit\Auth\Authorization;

use Therajatspace\Larakit\Auth\Contracts\DelegationConfigContract;

final class DelegationConfig implements DelegationConfigContract
{
    public function enabled(): bool
    {
        return (bool) config(
            'larakit.auth.delegation.enabled',
            true
        );
    }

    public function roleTargets(
        string $sourceRole
    ): array {
        return $this->targets(
            'roles',
            $sourceRole
        );
    }

    public function permissionTargets(
        string $sourceRole
    ): array {
        return $this->targets(
            'permissions',
            $sourceRole
        );
    }

    protected function targets(
        string $type,
        string $sourceRole
    ): array {
        $sourceRole = trim($sourceRole);

        if ($sourceRole === '') {
            return [];
        }

        $configured = config(
            "larakit.auth.delegation.{$type}.{$sourceRole}.assignable",
            []
        );

        if (! is_array($configured)) {
            return [];
        }

        return array_values(
            array_unique(
                array_filter(
                    array_map(
                        static fn ($value) => trim((string) $value),
                        $configured
                    ),
                    static fn ($value) => $value !== ''
                )
            )
        );
    }
}