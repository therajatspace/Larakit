<?php

namespace Tests\Unit\Admin\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;
use Therajatspace\Larakit\Admin\AdminServiceProvider;
use Therajatspace\Larakit\Admin\Users\UserManagementManager;
use Therajatspace\Larakit\LaraKitServiceProvider;
use Therajatspace\Larakit\Auth\AuthServiceProvider;

class UserManagementManagerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
            AuthServiceProvider::class,
            AdminServiceProvider::class,
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
            'larakit.auth.user.model',
            TestUser::class
        );

        $app['config']->set(
            'auth.defaults.guard',
            'web'
        );

        $app['config']->set(
            'larakit.admin.users.account.status_attribute',
            'is_active'
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
                $table->boolean('is_active')->default(true);
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

    protected function manager(): UserManagementManager
    {
        return $this->app->make(
            UserManagementManager::class
        );
    }

    public function test_user_model_is_returned_from_configuration(): void
    {
        $this->assertSame(
            TestUser::class,
            $this->manager()->userModel()
        );
    }

    public function test_missing_user_model_throws_exception(): void
    {
        config()->set(
            'larakit.auth.user.model',
            null
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $this->manager()->userModel();
    }

    public function test_empty_user_model_throws_exception(): void
    {
        config()->set(
            'larakit.auth.user.model',
            '   '
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $this->manager()->userModel();
    }

    public function test_nonexistent_user_model_throws_exception(): void
    {
        config()->set(
            'larakit.auth.user.model',
            'Tests\\Support\\Models\\DoesNotExist'
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $this->manager()->userModel();
    }

    public function test_non_eloquent_user_model_is_rejected(): void
    {
        config()->set(
            'larakit.auth.user.model',
            NonEloquentUser::class
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $this->manager()->userModel();
    }

    public function test_non_authenticatable_eloquent_model_is_rejected(): void
    {
        config()->set(
            'larakit.auth.user.model',
            NonAuthenticatableModel::class
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $this->manager()->userModel();
    }

    public function test_find_returns_null_when_user_does_not_exist(): void
    {
        $this->assertNull(
            $this->manager()->find(999999)
        );
    }

    public function test_find_returns_existing_user(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $result = $this->manager()->find(
            $user->getKey()
        );

        $this->assertInstanceOf(
            TestUser::class,
            $result
        );

        $this->assertSame(
            $user->getKey(),
            $result->getKey()
        );
    }

    public function test_paginate_returns_users(): void
    {
        TestUser::query()->create([
            'name' => 'User One',
            'email' => 'one@example.com',
        ]);

        TestUser::query()->create([
            'name' => 'User Two',
            'email' => 'two@example.com',
        ]);

        $result = $this->manager()->paginate(25);

        $this->assertSame(
            2,
            $result->total()
        );

        $this->assertCount(
            2,
            $result->items()
        );
    }

    public function test_paginate_respects_per_page(): void
    {
        foreach (range(1, 5) as $number) {
            TestUser::query()->create([
                'name' => "User {$number}",
                'email' => "user{$number}@example.com",
            ]);
        }

        $result = $this->manager()->paginate(2);

        $this->assertSame(
            5,
            $result->total()
        );

        $this->assertCount(
            2,
            $result->items()
        );
    }

    public function test_paginate_rejects_zero(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->manager()->paginate(0);
    }

    public function test_paginate_rejects_negative_values(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->manager()->paginate(-1);
    }

    public function test_paginate_rejects_values_above_one_hundred(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->manager()->paginate(101);
    }

    public function test_activate_changes_account_state(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'is_active' => false,
        ]);

        $this->manager()->activate($user);

        $user->refresh();

        $this->assertTrue(
            $user->is_active
        );
    }

    public function test_deactivate_changes_account_state(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'is_active' => true,
        ]);

        $this->manager()->deactivate($user);

        $user->refresh();

        $this->assertFalse(
            $user->is_active
        );
    }

    public function test_delete_removes_user(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $id = $user->getKey();

        $this->manager()->delete($user);

        $this->assertDatabaseMissing(
            'users',
            [
                'id' => $id,
            ]
        );
    }

    public function test_set_password_hashes_password(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $this->manager()->setPassword(
            $user,
            'New-password123!'
        );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                'New-password123!',
                $user->password
            )
        );

        $this->assertFalse(
            Hash::check(
                'old-password',
                $user->password
            )
        );
    }

    public function test_set_password_rejects_mismatched_confirmation(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $this->expectException(
            ValidationException::class
        );

        $this->manager()->setPassword(
            $user,
            'New-password123!',
            'Different-password123!'
        );
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        Role::create([
            'name' => 'editor',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'reviewer',
            'guard_name' => 'web',
        ]);

        $this->manager()->syncRoles(
            $user,
            [
                'editor',
                'reviewer',
            ]
        );

        $user->refresh();

        $this->assertTrue(
            $user->hasRole('editor')
        );

        $this->assertTrue(
            $user->hasRole('reviewer')
        );

        $this->assertCount(
            2,
            $user->roles
        );
    }

    public function test_role_can_be_removed_without_removing_other_roles(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        Role::create([
            'name' => 'editor',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'reviewer',
            'guard_name' => 'web',
        ]);

        $user->assignRole([
            'editor',
            'reviewer',
        ]);

        $this->manager()->removeRole(
            $user,
            'editor'
        );

        $user->refresh();

        $this->assertFalse(
            $user->hasRole('editor')
        );

        $this->assertTrue(
            $user->hasRole('reviewer')
        );
    }

    public function test_direct_permission_can_be_granted(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        Permission::create([
            'name' => 'reports.export',
            'guard_name' => 'web',
        ]);

        $this->manager()->givePermission(
            $user,
            'reports.export'
        );

        $user->refresh();

        $this->assertTrue(
            $user->hasDirectPermission(
                'reports.export'
            )
        );
    }

    public function test_direct_permission_can_be_revoked(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        Permission::create([
            'name' => 'reports.export',
            'guard_name' => 'web',
        ]);

        $user->givePermissionTo(
            'reports.export'
        );

        $this->manager()->revokePermission(
            $user,
            'reports.export'
        );

        $user->refresh();

        $this->assertFalse(
            $user->hasDirectPermission(
                'reports.export'
            )
        );
    }

    public function test_permission_inherited_from_role_is_preserved_when_direct_permission_is_revoked(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $role = Role::create([
            'name' => 'editor',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'reports.export',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo(
            'reports.export'
        );

        $user->assignRole(
            'editor'
        );

        $user->givePermissionTo(
            'reports.export'
        );

        $this->manager()->revokePermission(
            $user,
            'reports.export'
        );

        $user->refresh();

        $this->assertFalse(
            $user->hasDirectPermission(
                'reports.export'
            )
        );

        $this->assertTrue(
            $user->hasPermissionTo(
                'reports.export'
            )
        );
    }

    public function test_sync_roles_replaces_existing_roles(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        Role::create([
            'name' => 'editor',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'reviewer',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'manager',
            'guard_name' => 'web',
        ]);

        $user->assignRole([
            'editor',
            'reviewer',
        ]);

        $this->manager()->syncRoles(
            $user,
            [
                'manager',
            ]
        );

        $user->refresh();

        $this->assertFalse(
            $user->hasRole('editor')
        );

        $this->assertFalse(
            $user->hasRole('reviewer')
        );

        $this->assertTrue(
            $user->hasRole('manager')
        );
    }

    public function test_sync_permissions_replaces_existing_direct_permissions(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        Permission::create([
            'name' => 'reports.view',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'reports.export',
            'guard_name' => 'web',
        ]);

        $user->givePermissionTo([
            'reports.view',
            'reports.export',
        ]);

        $this->manager()->syncPermissions(
            $user,
            [
                'reports.view',
            ]
        );

        $user->refresh();

        $this->assertTrue(
            $user->hasDirectPermission(
                'reports.view'
            )
        );

        $this->assertFalse(
            $user->hasDirectPermission(
                'reports.export'
            )
        );
    }
}

class TestUser extends Authenticatable
{
    use HasRoles;

    protected $table = 'users';

    protected $guarded = [];

    protected $guard_name = 'web';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}

class NonEloquentUser
{
}

class NonAuthenticatableModel extends Model
{
    protected $table = 'users';
}