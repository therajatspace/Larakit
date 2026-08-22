<?php

namespace Therajatspace\Larakit\Auth\Login;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Therajatspace\Larakit\Auth\Contracts\AuthContextContract;
use Therajatspace\Larakit\Auth\Contracts\LoginRateLimiterContract;

class LoginRateLimiter implements LoginRateLimiterContract
{
    public function __construct(
        protected RateLimiter $limiter,
        protected AuthContextContract $context,
        protected Request $request,
    ) {
    }

    public function enabled(): bool
    {
        return (bool) config(
            'larakit.auth.rate_limit.enabled',
            true
        );
    }

    public function maxAttempts(): int
    {
        return $this->accountMaxAttempts();
    }

    public function decaySeconds(): int
    {
        return $this->accountDecaySeconds();
    }

    public function accountMaxAttempts(): int
    {
        return max(
            1,
            (int) config(
                'larakit.auth.rate_limit.account.max_attempts',
                5
            )
        );
    }

    public function accountDecaySeconds(): int
    {
        return max(
            1,
            (int) config(
                'larakit.auth.rate_limit.account.decay_seconds',
                60
            )
        );
    }

    public function ipMaxAttempts(): int
    {
        return max(
            1,
            (int) config(
                'larakit.auth.rate_limit.ip.max_attempts',
                30
            )
        );
    }

    public function ipDecaySeconds(): int
    {
        return max(
            1,
            (int) config(
                'larakit.auth.rate_limit.ip.decay_seconds',
                60
            )
        );
    }

    public function tooManyAttempts(
        string $email
    ): bool {
        if (! $this->enabled()) {
            return false;
        }

        return $this->limiter->tooManyAttempts(
            $this->accountKey($email),
            $this->accountMaxAttempts()
        ) || $this->limiter->tooManyAttempts(
            $this->ipKey(),
            $this->ipMaxAttempts()
        );
    }

    public function hit(
        string $email
    ): int {
        if (! $this->enabled()) {
            return 0;
        }

        $accountAttempts = $this->limiter->hit(
            $this->accountKey($email),
            $this->accountDecaySeconds()
        );

        $this->limiter->hit(
            $this->ipKey(),
            $this->ipDecaySeconds()
        );

        return $accountAttempts;
    }

    public function clear(
        string $email
    ): void {
        if (! $this->enabled()) {
            return;
        }

        $this->limiter->clear(
            $this->accountKey($email)
        );

        /*
         * Do not clear the IP bucket after a successful login.
         *
         * The IP bucket protects the application from a client
         * generating a large number of authentication attempts
         * across different accounts.
         */
    }

    public function attempts(
        string $email
    ): int {
        if (! $this->enabled()) {
            return 0;
        }

        return $this->limiter->attempts(
            $this->accountKey($email)
        );
    }

    public function ipAttempts(): int
    {
        if (! $this->enabled()) {
            return 0;
        }

        return $this->limiter->attempts(
            $this->ipKey()
        );
    }

    protected function accountKey(
        string $email
    ): string {
        return 'larakit:auth:login:account:'
            . $this->context->guard()
            . ':'
            . hash(
                'sha256',
                Str::lower(trim($email))
            );
    }

    protected function ipKey(): string
    {
        $ip = $this->request->ip() ?? 'unknown';

        return 'larakit:auth:login:ip:'
            . $this->context->guard()
            . ':'
            . hash(
                'sha256',
                $ip
            );
    }
}