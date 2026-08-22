<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Registration;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\Auth\Registration\RegistrationData;

class RegistrationDataTest extends TestCase
{
    public function test_registration_data_contains_only_expected_values(): void
    {
        $data = RegistrationData::make(
            name: 'Test User',
            email: 'test@example.com',
            password: 'password',
            profile: 'public',
        );

        $this->assertSame(
            'Test User',
            $data->name
        );

        $this->assertSame(
            'test@example.com',
            $data->email
        );

        $this->assertSame(
            'password',
            $data->password
        );

        $this->assertSame(
            'public',
            $data->profile
        );
    }

    public function test_profile_is_optional(): void
    {
        $data = RegistrationData::make(
            name: 'Test User',
            email: 'test@example.com',
            password: 'password',
        );

        $this->assertNull(
            $data->profile
        );
    }

    public function test_registration_data_is_immutable(): void
    {
        $reflection = new \ReflectionClass(
            RegistrationData::class
        );

        $name = $reflection->getProperty('name');
        $email = $reflection->getProperty('email');
        $password = $reflection->getProperty('password');
        $profile = $reflection->getProperty('profile');

        $this->assertTrue($name->isReadOnly());
        $this->assertTrue($email->isReadOnly());
        $this->assertTrue($password->isReadOnly());
        $this->assertTrue($profile->isReadOnly());
    }
}