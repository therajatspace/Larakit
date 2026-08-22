<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Profiles;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\Auth\Profiles\AuthProfile;

class AuthProfileTest extends TestCase
{
    public function test_profile_exposes_configuration(): void
    {
        $profile = new AuthProfile('internal', [
            'role' => 'staff',
            'login' => true,
            'registration' => true,
        ]);

        $this->assertSame('internal', $profile->name());
        $this->assertSame('staff', $profile->role());
        $this->assertTrue($profile->loginEnabled());
        $this->assertTrue($profile->registrationEnabled());
    }

    public function test_role_is_optional(): void
    {
        $profile = new AuthProfile('public');

        $this->assertNull($profile->role());
    }

    public function test_login_is_enabled_by_default(): void
    {
        $profile = new AuthProfile('public');

        $this->assertTrue($profile->loginEnabled());
    }

    public function test_registration_is_disabled_by_default(): void
    {
        $profile = new AuthProfile('public');

        $this->assertFalse($profile->registrationEnabled());
    }

    public function test_empty_profile_name_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AuthProfile('');
    }

    public function test_empty_role_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AuthProfile('public', [
            'role' => '',
        ]);
    }
}