<?php

namespace Therajatspace\Larakit\Auth\Contracts;

interface DelegationConfigContract
{
    public function enabled(): bool;

    public function roleTargets(
        string $sourceRole
    ): array;

    public function permissionTargets(
        string $sourceRole
    ): array;
}