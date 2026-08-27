<?php

namespace Tests\Unit\Admin\Users\Detail;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\Users\Contracts\UserAccountDriverContract;
use Therajatspace\Larakit\Admin\Users\Data\UserDetailData;
use Therajatspace\Larakit\Admin\Users\Detail\UserDetailService;
use Therajatspace\Larakit\Admin\Users\UserManagementManager;
use Therajatspace\Larakit\Auth\Contracts\UserAuthorizationManagerContract;

class UserDetailServiceTest extends TestCase
{
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
            'larakit.admin.users.identity.display_name_attribute',
            'name'
        );

        $app['config']->set(
            'larakit.admin.users.identity.email_attribute',
            'email'
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::connection('testing')->create(
            'users',
            function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('email')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('password')->nullable();
                $table->timestamps();
            }
        );
    }

    protected function tearDown(): void
    {
        Schema::connection('testing')->dropIfExists(
            'users'
        );

        parent::tearDown();
    }

    protected function service(
        bool $active = true,
        array $roles = [],
        array $permissions = [],
        bool $supportsRoles = true,
        bool $supportsPermissions = true
    ): UserDetailService {
        return new UserDetailService(
            $this->app->make(
                UserManagementManager::class
            ),
            new FakeAccountDriver(
                $active
            ),
            new FakeUserAuthorizationManager(
                roles: $roles,
                permissions: $permissions,
                supportsRoles: $supportsRoles,
                supportsPermissions: $supportsPermissions
            )
        );
    }

    public function test_find_returns_user_detail_data(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $result = $this
            ->service()
            ->find($user->id);

        $this->assertInstanceOf(
            UserDetailData::class,
            $result
        );

        $this->assertSame(
            $user->id,
            $result->id
        );

        $this->assertSame(
            'John Doe',
            $result->displayName
        );

        $this->assertSame(
            'john@example.com',
            $result->email
        );
    }

    public function test_find_returns_null_for_missing_user(): void
    {
        $result = $this
            ->service()
            ->find(999999);

        $this->assertNull(
            $result
        );
    }

    public function test_account_status_is_included(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $result = $this
            ->service(active: true)
            ->find($user->id);

        $this->assertTrue(
            $result->accountStatus
        );
    }

    public function test_inactive_account_status_is_included(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $result = $this
            ->service(active: false)
            ->find($user->id);

        $this->assertFalse(
            $result->accountStatus
        );
    }

    public function test_roles_are_included(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $result = $this
            ->service(
                roles: [
                    'editor',
                    'reviewer',
                ]
            )
            ->find($user->id);

        $this->assertSame(
            [
                'editor',
                'reviewer',
            ],
            $result->roles
        );
    }

    public function test_roles_are_normalized(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $result = $this
            ->service(
                roles: [
                    ' editor ',
                    '',
                    'editor',
                    'reviewer ',
                    '   ',
                ]
            )
            ->find($user->id);

        $this->assertSame(
            [
                'editor',
                'reviewer',
            ],
            $result->roles
        );
    }

    public function test_permissions_are_included(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $result = $this
            ->service(
                permissions: [
                    'posts.view',
                    'posts.edit',
                ]
            )
            ->find($user->id);

        $this->assertSame(
            [
                'posts.view',
                'posts.edit',
            ],
            $result->permissions
        );
    }

    public function test_permissions_are_normalized(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $result = $this
            ->service(
                permissions: [
                    ' posts.view ',
                    '',
                    'posts.view',
                    'posts.edit ',
                    '   ',
                ]
            )
            ->find($user->id);

        $this->assertSame(
            [
                'posts.view',
                'posts.edit',
            ],
            $result->permissions
        );
    }

    public function test_roles_are_empty_when_role_support_is_unavailable(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $result = $this
            ->service(
                roles: [
                    'editor',
                ],
                supportsRoles: false
            )
            ->find($user->id);

        $this->assertSame(
            [],
            $result->roles
        );
    }

    public function test_permissions_are_empty_when_permission_support_is_unavailable(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $result = $this
            ->service(
                permissions: [
                    'posts.view',
                ],
                supportsPermissions: false
            )
            ->find($user->id);

        $this->assertSame(
            [],
            $result->permissions
        );
    }

    public function test_timestamps_are_included(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $result = $this
            ->service()
            ->find($user->id);

        $this->assertSame(
            $user->created_at->toDateTimeString(),
            $result->createdAt->toDateTimeString()
        );

        $this->assertSame(
            $user->updated_at->toDateTimeString(),
            $result->updatedAt->toDateTimeString()
        );
    }

    public function test_from_user_can_build_detail_without_database_lookup(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $service = $this->service(
            active: true,
            roles: [
                'editor',
            ],
            permissions: [
                'posts.view',
            ]
        );

        $result = $service->fromUser(
            $user
        );

        $this->assertSame(
            $user->id,
            $result->id
        );

        $this->assertSame(
            [
                'editor',
            ],
            $result->roles
        );

        $this->assertSame(
            [
                'posts.view',
            ],
            $result->permissions
        );
    }
}

class FakeAccountDriver implements UserAccountDriverContract
{
    public function __construct(
        protected bool $active = true
    ) {
    }

    public function activate(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): void {
    }

    public function deactivate(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): void {
    }

    public function isActive(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): bool {
        return $this->active;
    }

    public function delete(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): void {
    }
}

class FakeUserAuthorizationManager
    implements UserAuthorizationManagerContract
{
    public function __construct(
        protected array $roles = [],
        protected array $permissions = [],
        protected bool $supportsRoles = true,
        protected bool $supportsPermissions = true
    ) {
    }

    public function assignRole(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        string $role
    ): void {
    }

    public function removeRole(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        string $role
    ): void {
    }

    public function syncRoles(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        array $roles
    ): void {
    }

    public function hasRole(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        string $role
    ): bool {
        return in_array(
            $role,
            $this->roles,
            true
        );
    }

    public function supportsRoles(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): bool {
        return $this->supportsRoles;
    }

    public function roles(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): array {
        return $this->roles;
    }

    public function givePermission(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        string $permission
    ): void {
    }

    public function revokePermission(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        string $permission
    ): void {
    }

    public function syncPermissions(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        array $permissions
    ): void {
    }

    public function hasPermission(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        string $permission
    ): bool {
        return in_array(
            $permission,
            $this->permissions,
            true
        );
    }

    public function supportsPermissions(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): bool {
        return $this->supportsPermissions;
    }

    public function permissions(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): array {
        return $this->permissions;
    }
}

class TestUser extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];
}
