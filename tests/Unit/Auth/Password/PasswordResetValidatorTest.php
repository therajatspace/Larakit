<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Password;

use Illuminate\Validation\ValidationException;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\Password\ForgotPasswordData;
use Therajatspace\Larakit\Auth\Password\PasswordResetValidator;
use Therajatspace\Larakit\Auth\Password\ResetPasswordData;
use Therajatspace\Larakit\LaraKitServiceProvider;

class PasswordResetValidatorTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
        ];
    }

    public function test_valid_forgot_password_data_is_accepted(): void
    {
        $validator = $this->app->make(
            PasswordResetValidator::class
        );

        $validator->validateForgot(
            ForgotPasswordData::make(
                'john@example.com'
            )
        );

        $this->assertTrue(true);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->expectException(
            ValidationException::class
        );

        $validator = $this->app->make(
            PasswordResetValidator::class
        );

        $validator->validateForgot(
            ForgotPasswordData::make('invalid')
        );
    }

    public function test_valid_reset_data_is_accepted(): void
    {
        $validator = $this->app->make(
            PasswordResetValidator::class
        );

        $validator->validateReset(
            ResetPasswordData::make(
                'token',
                'john@example.com',
                'StrongPassword123!',
                'StrongPassword123!'
            )
        );

        $this->assertTrue(true);
    }

    public function test_password_confirmation_must_match(): void
    {
        $this->expectException(
            ValidationException::class
        );

        $validator = $this->app->make(
            PasswordResetValidator::class
        );

        $validator->validateReset(
            ResetPasswordData::make(
                'token',
                'john@example.com',
                'StrongPassword123!',
                'DifferentPassword123!'
            )
        );
    }

    public function test_token_is_required(): void
    {
        $this->expectException(
            ValidationException::class
        );

        $validator = $this->app->make(
            PasswordResetValidator::class
        );

        $validator->validateReset(
            ResetPasswordData::make(
                '',
                'john@example.com',
                'StrongPassword123!',
                'StrongPassword123!'
            )
        );
    }
}
