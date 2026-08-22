<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Authorization;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\Auth\Authorization\AuthorizationPermissions;

class AuthorizationPermissionsTest extends TestCase
{
    public function test_all_returns_every_management_permission(): void
    {
        $permissions = AuthorizationPermissions::all();

        $this->assertContains(
            AuthorizationPermissions::USERS_MANAGE,
            $permissions
        );

        $this->assertContains(
            AuthorizationPermissions::ROLES_ASSIGN,
            $permissions
        );

        $this->assertContains(
            AuthorizationPermissions::PERMISSIONS_ASSIGN,
            $permissions
        );
    }

    public function test_all_contains_no_duplicates(): void
    {
        $permissions = AuthorizationPermissions::all();

        $this->assertCount(
            count(array_unique($permissions)),
            $permissions
        );
    }

    public function test_all_contains_expected_number_of_permissions(): void
    {
        $this->assertCount(
            17,
            AuthorizationPermissions::all()
        );
    }
}