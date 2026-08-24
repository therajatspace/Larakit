<?php

namespace Therajatspace\Larakit\Auth\Contracts;

use Illuminate\Contracts\Auth\MustVerifyEmail;

interface EmailVerificationServiceContract
{
    public function send(
        MustVerifyEmail $user
    ): void;

    public function verify(
        MustVerifyEmail $user
    ): bool;
}
