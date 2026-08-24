<?php

namespace Therajatspace\Larakit\Auth\Verification;

use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use RuntimeException;
use Therajatspace\Larakit\Auth\Contracts\EmailVerificationServiceContract;

class EmailVerificationService implements EmailVerificationServiceContract
{
    public function send(
        MustVerifyEmail $user
    ): void {
        $this->ensureEnabled();

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }

    public function verify(
        MustVerifyEmail $user
    ): bool {
        $this->ensureEnabled();

        if ($user->hasVerifiedEmail()) {
            return false;
        }

        if (! $user->markEmailAsVerified()) {
            return false;
        }

        event(
            new Verified($user)
        );

        return true;
    }

    public function verificationUrl(
        MustVerifyEmail $user
    ): string {
        $this->ensureEnabled();

        if ($user->hasVerifiedEmail()) {
            throw new RuntimeException(
                'The user email address is already verified.'
            );
        }

        return URL::temporarySignedRoute(
            'larakit.verification.verify',
            Carbon::now()->addMinutes(
                $this->expiration()
            ),
            [
                'id' => $user->getKey(),
                'hash' => sha1(
                    $user->getEmailForVerification()
                ),
            ]
        );
    }

    public function expiration(): int
    {
        return max(
            1,
            (int) config(
                'larakit.auth.email_verification.expiration',
                60
            )
        );
    }

    public function throttle(): int
    {
        return max(
            1,
            (int) config(
                'larakit.auth.email_verification.throttle',
                60
            )
        );
    }

    protected function ensureEnabled(): void
    {
        if (! config(
            'larakit.auth.email_verification.enabled',
            true
        )) {
            throw new RuntimeException(
                'LaraKit email verification is disabled.'
            );
        }
    }
}
