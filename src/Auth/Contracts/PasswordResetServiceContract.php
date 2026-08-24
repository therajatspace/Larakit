<?php

namespace Therajatspace\Larakit\Auth\Contracts;

use Therajatspace\Larakit\Auth\Password\ForgotPasswordData;
use Therajatspace\Larakit\Auth\Password\ResetPasswordData;

interface PasswordResetServiceContract
{
    public function sendResetLink(
        ForgotPasswordData $data
    ): string;

    public function reset(
        ResetPasswordData $data
    ): string;
}
