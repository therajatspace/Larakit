<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Password;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\Auth\Password\ForgotPasswordData;
use Therajatspace\Larakit\Auth\Password\ResetPasswordData;

class PasswordResetDataTest extends TestCase
{
    public function test_forgot_email_is_normalized(): void
    {
        $data = ForgotPasswordData::make(
            '  JOHN@EXAMPLE.COM '
        );

        $this->assertSame(
            'john@example.com',
            $data->email
        );
    }

    public function test_reset_email_and_token_are_normalized(): void
    {
        $data = ResetPasswordData::make(
            '  token ',
            '  JOHN@EXAMPLE.COM ',
            'password',
            'password'
        );

        $this->assertSame(
            'token',
            $data->token
        );

        $this->assertSame(
            'john@example.com',
            $data->email
        );
    }
}
