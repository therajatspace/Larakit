<?php

namespace Tests\Unit\Auth\Authorization;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;
use Therajatspace\Larakit\Auth\AuthServiceProvider;
use Therajatspace\Larakit\Auth\Authorization\UserAuthorizationManager;
use Therajatspace\Larakit\Auth\Contracts\UserAuthorizationManagerContract;
use Therajatspace\Larakit\LaraKitServiceProvider;

class UserAuthorizationManagerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
            AuthServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set(
            'database.default',
            'testing'
        );

        $app['config']->set(
            'database.connections.testing',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );

        $app['config']->set(
            'auth.defaults.guard',
            'web'
        );

        $app['config']->set(
            'permission.models.permission',
            Permission::class
        );

        $app['config']->set(
            'permission.models.role',
            Role::class
        );

        $app['config']->set(
            'permission.table_names.roles',
            'roles'
        );

        $app['config']->set(
            'permission.table_names.permissions',
            'permissions'
        );

        $app['config']->set(
            'permission.table_names.model_has_roles',
            'model_has_roles'
        );

        $app['config']->set(
            'permission.table_names.model_has_permissions',
            'model_has_permissions'
        );

        $app['config']->set(
            'permission.table_names.role_has_permissions',
            'role_has_permissions'
        );

        $app['config']->set(
            'permission.column_names.model_morph_key',
            'model_id'
        );

        $app['config']->set(
            'permission.column_names.team_foreign_key',
            'team_id'
        );

        $app['config']->set(
            'permission.teams',
            false
        );

        $app['config']->set(
            'permission.register_permission_check_method',
            true
        );

        $app['config']->set(
            'permission.display_permission_in_exception',
            false
        );

        $app['config']->set(
            'permission.display_role_in_exception',
            false
        );

        $app['config']->set(
            'permission.testing',
            true
        );

        $app['config']->set(
            'permission.cache.key',
            'spatie.permission.cache'
        );

        $app['config']->set(
            'permission.cache.store',
            'array'
        );

        $app['config']->set(
            'permission.cache.expiration_time',
            3600
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->createTestTables();

        $this->clearPermissionCache();
    }

    protected function tearDown(): void
    {
        $this->clearPermissionCache();

        Schema::dropIfExists(
            'role_has_permissions'
        );

        Schema::dropIfExists(
            'model_has_permissions'
        );

        Schema::dropIfExists(
            'model_has_roles'
        );

        Schema::dropIfExists(
            'permissions'
        );

        Schema::dropIfExists(
            'roles'
        );

        Schema::dropIfExists(
            'users'
        );

        parent::tearDown();
    }

    protected function createTestTables(): void
    {
        Schema::create(
            'users',
            function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password')->nullable();
                $table->rememberToken();
                $table->timestamps();
            }
        );

        Schema::create(
            'roles',
            function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();

                $table->unique([
                    'name',
                    'guard_name',
                ]);
            }
        );

        Schema::create(
            'permissions',
            function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();

                $table->unique([
                    'name',
                    'guard_name',
                ]);
            }
        );

        Schema::create(
            'model_has_roles',
            function (Blueprint $table): void {
                $table->unsignedBigInteger('role_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');

                $table->index([
                    'model_id',
                    'model_type',
                ]);

                $table->primary([
                    'role_id',
                    'model_id',
                    'model_type',
                ]);
            }
        );

        Schema::create(
            'model_has_permissions',
            function (Blueprint $table): void {
                $table->unsignedBigInteger('permission_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');

                $table->index([
                    'model_id',
                    'model_type',
                ]);

                $table->primary([
                    'permission_id',
                    'model_id',
                    'model_type',
                ]);
            }
        );

        Schema::create(
            'role_has_permissions',
            function (Blueprint $table): void {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');

                $table->primary([
                    'permission_id',
                    'role_id',
                ]);
            }
        );
    }

    protected function clearPermissionCache(): void
    {
        if ($this->app->bound(PermissionRegistrar::class)) {
            $this->app
                ->make(PermissionRegistrar::class)
                ->forgetCachedPermissions();
        }
    }

    protected function manager(): UserAuthorizationManager
    {
        return $this->app->make(
            UserAuthorizationManager::class
        );
    }

    protected function user(): TestUser
    {
        return TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);
    }

    protected function createPermission(
        string $name
    ): Permission {
        return Permission::create([
            'name' => $name,
            'guard_name' => 'web',
        ]);
    }

    protected function createRole(
        string $name
    ): Role {
        return Role::create([
            'name' => $name,
            'guard_name' => 'web',
        ]);
    }

    public function test_manager_can_be_resolved(): void
    {
        $manager = $this->manager();

        $this->assertInstanceOf(
            UserAuthorizationManager::class,
            $manager
        );
    }

    public function test_contract_can_be_resolved(): void
    {
        $manager = $this->app->make(
            UserAuthorizationManagerContract::class
        );

        $this->assertInstanceOf(
            UserAuthorizationManager::class,
            $manager
        );
    }

    public function test_empty_role_is_rejected(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->assignRole(
            $user,
            '   '
        );
    }

    public function test_empty_permission_is_rejected(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->givePermission(
            $user,
            '   '
        );
    }

    public function test_empty_role_is_rejected_when_removed(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->removeRole(
            $user,
            '   '
        );
    }

    public function test_empty_permission_is_rejected_when_revoked(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->revokePermission(
            $user,
            '   '
        );
    }

    public function test_roles_returns_empty_array_when_user_has_no_roles(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $this->assertSame(
            [],
            $manager->roles($user)
        );
    }

    public function test_roles_returns_assigned_role_names(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $this->createRole('editor');
        $this->createRole('reviewer');

        $user->assignRole([
            'editor',
            'reviewer',
        ]);

        $this->assertSame(
            [
                'editor',
                'reviewer',
            ],
            $manager->roles($user)
        );
    }

    public function test_roles_are_normalized_to_strings(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $this->createRole('editor');

        $user->assignRole('editor');

        $roles = $manager->roles($user);

        $this->assertIsArray(
            $roles
        );

        $this->assertContains(
            'editor',
            $roles
        );

        foreach ($roles as $role) {
            $this->assertIsString(
                $role
            );
        }
    }

    public function test_roles_reject_user_without_role_support(): void
    {
        $manager = $this->manager();

        $user = new UnsupportedUser();

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->roles($user);
    }

    public function test_supports_roles_returns_true_for_supported_user(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $this->assertTrue(
            $manager->supportsRoles($user)
        );
    }

    public function test_supports_roles_returns_false_for_unsupported_user(): void
    {
        $manager = $this->manager();

        $user = new UnsupportedUser();

        $this->assertFalse(
            $manager->supportsRoles($user)
        );
    }

    public function test_permissions_returns_empty_array_when_user_has_no_permissions(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $this->assertSame(
            [],
            $manager->permissions($user)
        );
    }

    public function test_permissions_returns_direct_permissions(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $this->createPermission(
            'reports.view'
        );

        $this->createPermission(
            'reports.export'
        );

        $user->givePermissionTo([
            'reports.view',
            'reports.export',
        ]);

        $this->assertSame(
            [
                'reports.view',
                'reports.export',
            ],
            $manager->permissions($user)
        );
    }

    public function test_permissions_include_permissions_inherited_from_roles(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $role = $this->createRole(
            'editor'
        );

        $this->createPermission(
            'reports.view'
        );

        $role->givePermissionTo(
            'reports.view'
        );

        $user->assignRole(
            'editor'
        );

        $this->assertSame(
            [
                'reports.view',
            ],
            $manager->permissions($user)
        );
    }

    public function test_permissions_include_direct_and_inherited_permissions(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $role = $this->createRole(
            'editor'
        );

        $this->createPermission(
            'reports.view'
        );

        $this->createPermission(
            'reports.export'
        );

        $role->givePermissionTo(
            'reports.view'
        );

        $user->assignRole(
            'editor'
        );

        $user->givePermissionTo(
            'reports.export'
        );

        $permissions = $manager->permissions(
            $user
        );

        $this->assertSame(
            [
                'reports.view',
                'reports.export',
            ],
            $permissions
        );
    }

    public function test_permissions_are_unique_when_direct_and_inherited_permission_overlap(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $role = $this->createRole(
            'editor'
        );

        $this->createPermission(
            'reports.view'
        );

        $role->givePermissionTo(
            'reports.view'
        );

        $user->assignRole(
            'editor'
        );

        $user->givePermissionTo(
            'reports.view'
        );

        $permissions = $manager->permissions(
            $user
        );

        $this->assertSame(
            [
                'reports.view',
            ],
            $permissions
        );
    }

    public function test_permissions_are_normalized_to_strings(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $this->createPermission(
            'reports.view'
        );

        $user->givePermissionTo(
            'reports.view'
        );

        $permissions = $manager->permissions(
            $user
        );

        $this->assertIsArray(
            $permissions
        );

        foreach ($permissions as $permission) {
            $this->assertIsString(
                $permission
            );
        }

        $this->assertContains(
            'reports.view',
            $permissions
        );
    }

    public function test_supports_permissions_returns_true_for_supported_user(): void
    {
        $manager = $this->manager();

        $user = $this->user();

        $this->assertTrue(
            $manager->supportsPermissions($user)
        );
    }

    public function test_supports_permissions_returns_false_for_unsupported_user(): void
    {
        $manager = $this->manager();

        $user = new UnsupportedUser();

        $this->assertFalse(
            $manager->supportsPermissions($user)
        );
    }

    public function test_permissions_reject_user_without_permission_support(): void
    {
        $manager = $this->manager();

        $user = new UnsupportedUser();

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->permissions($user);
    }
}

class TestUser extends Authenticatable
{
    use HasRoles;

    protected $table = 'users';

    protected $guarded = [];

    protected string $guard_name = 'web';
}

class UnsupportedUser extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];
}