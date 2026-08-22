<?php

namespace Therajatspace\Larakit\Auth\Contracts;

interface LoginRateLimiterContract
{
    public function enabled(): bool;

    public function maxAttempts(): int;

    public function decaySeconds(): int;

    public function accountMaxAttempts(): int;

    public function accountDecaySeconds(): int;

    public function ipMaxAttempts(): int;

    public function ipDecaySeconds(): int;

    public function tooManyAttempts(
        string $email
    ): bool;

    public function hit(
        string $email
    ): int;

    public function clear(
        string $email
    ): void;

    public function attempts(
        string $email
    ): int;

    public function ipAttempts(): int;
}