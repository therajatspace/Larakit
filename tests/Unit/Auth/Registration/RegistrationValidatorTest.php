<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Registration;

use Illuminate\Validation\ValidationException;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\Registration\RegistrationData;
use Therajatspace\Larakit\Auth\Registration\RegistrationValidator;
use Therajatspace\Larakit\LaraKitServiceProvider;

class RegistrationValidatorTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config()->set(
            'larakit.auth.profiles',
            [
                'public' => [
                    'role' => null,
                    'login' => true,
                    'registration' => true,
                ],

                'closed' => [
                    'role' => null,
                    'login' => true,
                    'registration' => false,
                ],
            ]
        );
    }

    public function test_valid_registration_data_passes(): void
    {
        $data = RegistrationData::make(
            name: 'Test User',
            email: 'test@example.com',
            password: 'StrongPassword123!',
            profile: 'public',
        );

        $validator = $this->app->make(
            RegistrationValidator::class
        );

        $validator->validate($data);

        $this->addToAssertionCount(1);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $data = RegistrationData::make(
            name: 'Test User',
            email: 'invalid-email',
            password: 'StrongPassword123!',
            profile: 'public',
        );

        $validator = $this->app->make(
            RegistrationValidator::class
        );

        $this->expectException(
            ValidationException::class
        );

        $validator->validate($data);
    }

    public function test_empty_name_is_rejected(): void
    {
        $data = RegistrationData::make(
            name: '',
            email: 'test@example.com',
            password: 'StrongPassword123!',
            profile: 'public',
        );

        $validator = $this->app->make(
            RegistrationValidator::class
        );

        $this->expectException(
            ValidationException::class
        );

        $validator->validate($data);
    }

    public function test_invalid_profile_is_rejected(): void
    {
        $data = RegistrationData::make(
            name: 'Test User',
            email: 'test@example.com',
            password: 'StrongPassword123!',
            profile: 'does-not-exist',
        );

        $validator = $this->app->make(
            RegistrationValidator::class
        );

        $this->expectException(
            ValidationException::class
        );

        $validator->validate($data);
    }

    public function test_registration_disabled_profile_is_rejected(): void
    {
        $data = RegistrationData::make(
            name: 'Test User',
            email: 'test@example.com',
            password: 'StrongPassword123!',
            profile: 'closed',
        );

        $validator = $this->app->make(
            RegistrationValidator::class
        );

        $this->expectException(
            ValidationException::class
        );

        $validator->validate($data);
    }

    public function test_profile_is_optional(): void
    {
        $data = RegistrationData::make(
            name: 'Test User',
            email: 'test@example.com',
            password: 'StrongPassword123!',
        );

        $validator = $this->app->make(
            RegistrationValidator::class
        );

        $validator->validate($data);

        $this->addToAssertionCount(1);
    }

    public function test_password_that_does_not_meet_the_default_policy_is_rejected(): void
    {
        $data = RegistrationData::make(
            name: 'Test User',
            email: 'test@example.com',
            password: '123',
            profile: 'public',
        );

        $validator = $this->app->make(
            RegistrationValidator::class
        );

        $this->expectException(
            ValidationException::class
        );

        $validator->validate($data);
    }
}