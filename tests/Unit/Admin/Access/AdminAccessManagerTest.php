<?php

namespace Tests\Unit\Admin\Access;

use Illuminate\Foundation\Auth\User as Authenticatable;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\Access\AdminAccessManager;
use Therajatspace\Larakit\Admin\Contracts\AdminAccessManagerContract;

class AdminAccessManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set(
            'larakit.admin.access.permission',
            'admin.access'
        );
    }

    public function test_manager_is_bound_to_contract(): void
    {
        $this->app->singleton(
            AdminAccessManagerContract::class,
            AdminAccessManager::class
        );

        $manager = $this->app->make(
            AdminAccessManagerContract::class
        );

        $this->assertInstanceOf(
            AdminAccessManager::class,
            $manager
        );
    }

    public function test_permission_is_returned(): void
    {
        $manager = new AdminAccessManager();

        $this->assertSame(
            'admin.access',
            $manager->permission()
        );
    }

    public function test_permission_is_trimmed(): void
    {
        config()->set(
            'larakit.admin.access.permission',
            '  admin.access  '
        );

        $manager = new AdminAccessManager();

        $this->assertSame(
            'admin.access',
            $manager->permission()
        );
    }

    public function test_empty_permission_becomes_null(): void
    {
        config()->set(
            'larakit.admin.access.permission',
            '   '
        );

        $manager = new AdminAccessManager();

        $this->assertNull(
            $manager->permission()
        );
    }

    public function test_null_permission_is_allowed(): void
    {
        config()->set(
            'larakit.admin.access.permission',
            null
        );

        $manager = new AdminAccessManager();

        $this->assertNull(
            $manager->permission()
        );
    }

    public function test_invalid_permission_configuration_throws_exception(): void
    {
        config()->set(
            'larakit.admin.access.permission',
            ['admin.access']
        );

        $manager = new AdminAccessManager();

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->permission();
    }

    public function test_unauthenticated_user_cannot_access_admin(): void
    {
        $manager = new AdminAccessManager();

        $this->assertFalse(
            $manager->canAccess()
        );
    }

    public function test_user_without_can_method_cannot_access_admin(): void
    {
        $user = new class implements \Illuminate\Contracts\Auth\Authenticatable {
            public function getAuthIdentifierName()
            {
                return 'id';
            }

            public function getAuthIdentifier()
            {
                return 1;
            }

            public function getAuthPasswordName()
            {
                return 'password';
            }

            public function getAuthPassword()
            {
                return null;
            }

            public function getRememberToken()
            {
                return null;
            }

            public function setRememberToken($value)
            {
            }

            public function getRememberTokenName()
            {
                return 'remember_token';
            }
        };

        $this->be($user);

        $manager = new AdminAccessManager();

        $this->assertFalse(
            $manager->canAccess()
        );
    }

    public function test_user_with_permission_can_access_admin(): void
    {
        $user = new class extends Authenticatable {
            public function can($ability, $arguments = [])
            {
                return $ability === 'admin.access';
            }
        };

        $this->be($user);

        $manager = new AdminAccessManager();

        $this->assertTrue(
            $manager->canAccess()
        );
    }

    public function test_user_without_permission_cannot_access_admin(): void
    {
        $user = new class extends Authenticatable {
            public function can($ability, $arguments = [])
            {
                return false;
            }
        };

        $this->be($user);

        $manager = new AdminAccessManager();

        $this->assertFalse(
            $manager->canAccess()
        );
    }

    public function test_truthy_authorization_result_is_cast_to_boolean(): void
    {
        $user = new class extends Authenticatable {
            public function can($ability, $arguments = [])
            {
                return 'yes';
            }
        };

        $this->be($user);

        $manager = new AdminAccessManager();

        $this->assertTrue(
            $manager->canAccess()
        );
    }

    public function test_falsy_authorization_result_is_cast_to_boolean(): void
    {
        $user = new class extends Authenticatable {
            public function can($ability, $arguments = [])
            {
                return 0;
            }
        };

        $this->be($user);

        $manager = new AdminAccessManager();

        $this->assertFalse(
            $manager->canAccess()
        );
    }
}