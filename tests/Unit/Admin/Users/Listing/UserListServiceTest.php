<?php

namespace Tests\Unit\Admin\Users\Listing;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\Users\Contracts\UserAccountDriverContract;
use Therajatspace\Larakit\Admin\Users\Data\UserListData;
use Therajatspace\Larakit\Admin\Users\Listing\UserListService;
use Therajatspace\Larakit\Admin\Users\Queries\UserQuery;
use Therajatspace\Larakit\Admin\Users\UserManagementManager;
use Therajatspace\Larakit\Auth\Contracts\UserAuthorizationManagerContract;

class UserListServiceTest extends TestCase
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

        $app['config']->set(
            'larakit.admin.users.listing.searchable',
            [
                'name',
                'email',
            ]
        );

        $app['config']->set(
            'larakit.admin.users.listing.sortable',
            [
                'id',
                'name',
                'email',
                'created_at',
            ]
        );

        $app['config']->set(
            'larakit.admin.users.listing.default_sort',
            'created_at'
        );

        $app['config']->set(
            'larakit.admin.users.listing.default_direction',
            'desc'
        );

        $app['config']->set(
            'larakit.admin.users.listing.per_page',
            25
        );

        $app['config']->set(
            'larakit.admin.users.listing.max_per_page',
            100
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
                $table->string('password');
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
        array $permissions = []
    ): UserListService {
        $accountDriver = new FakeAccountDriver(
            $active
        );

        $authorization = new FakeUserAuthorizationManager(
            $roles,
            $permissions
        );

        $userManager = $this->app->make(
            UserManagementManager::class
        );

        $query = new UserQuery(
            $userManager
        );

        return new UserListService(
            $query,
            $accountDriver,
            $authorization
        );
    }

    public function test_paginated_users_are_converted_to_list_data(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $results = $this
            ->service()
            ->paginate();

        $this->assertCount(
            1,
            $results->items()
        );

        $this->assertInstanceOf(
            UserListData::class,
            $results->first()
        );
    }

    public function test_account_status_is_added_to_list_data(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $results = $this
            ->service(active: true)
            ->paginate();

        $this->assertTrue(
            $results->first()->accountStatus
        );
    }

    public function test_inactive_account_status_is_added_to_list_data(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $results = $this
            ->service(active: false)
            ->paginate();

        $this->assertFalse(
            $results->first()->accountStatus
        );
    }

    public function test_roles_are_added_to_list_data(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $results = $this
            ->service(
                roles: [
                    'editor',
                    'reviewer',
                ]
            )
            ->paginate();

        $this->assertSame(
            [
                'editor',
                'reviewer',
            ],
            $results->first()->roles
        );
    }

    public function test_user_without_roles_has_empty_role_list(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $results = $this
            ->service()
            ->paginate();

        $this->assertSame(
            [],
            $results->first()->roles
        );
    }

    public function test_roles_are_normalized(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $results = $this
            ->service(
                roles: [
                    ' editor ',
                    '',
                    'editor',
                    'reviewer ',
                    '   ',
                ]
            )
            ->paginate();

        $this->assertSame(
            [
                'editor',
                'reviewer',
            ],
            $results->first()->roles
        );
    }

    public function test_permissions_are_added_to_list_data(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $results = $this
            ->service(
                permissions: [
                    'posts.view',
                    'posts.edit',
                ]
            )
            ->paginate();

        $this->assertSame(
            [
                'posts.view',
                'posts.edit',
            ],
            $results->first()->permissions
        );
    }

    public function test_user_without_permissions_has_empty_permission_list(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $results = $this
            ->service()
            ->paginate();

        $this->assertSame(
            [],
            $results->first()->permissions
        );
    }

    public function test_permissions_are_normalized(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $results = $this
            ->service(
                permissions: [
                    ' posts.view ',
                    '',
                    'posts.view',
                    'posts.edit ',
                    '   ',
                ]
            )
            ->paginate();

        $this->assertSame(
            [
                'posts.view',
                'posts.edit',
            ],
            $results->first()->permissions
        );
    }

    public function test_permissions_are_not_requested_when_permission_support_is_unavailable(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $authorization = new FakeUserAuthorizationManager(
            roles: [],
            permissions: [
                'posts.view',
            ],
            supportsPermissions: false
        );

        $userManager = $this->app->make(
            UserManagementManager::class
        );

        $query = new UserQuery(
            $userManager
        );

        $service = new UserListService(
            $query,
            new FakeAccountDriver(),
            $authorization
        );

        $results = $service->paginate();

        $this->assertSame(
            [],
            $results->first()->permissions
        );

        $this->assertFalse(
            $authorization->permissionsCalled
        );
    }

    public function test_roles_are_not_requested_when_role_support_is_unavailable(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $authorization = new FakeUserAuthorizationManager(
            roles: [
                'editor',
            ],
            permissions: [],
            supportsRoles: false
        );

        $userManager = $this->app->make(
            UserManagementManager::class
        );

        $query = new UserQuery(
            $userManager
        );

        $service = new UserListService(
            $query,
            new FakeAccountDriver(),
            $authorization
        );

        $results = $service->paginate();

        $this->assertSame(
            [],
            $results->first()->roles
        );

        $this->assertFalse(
            $authorization->rolesCalled
        );
    }

    public function test_pagination_metadata_is_preserved(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            TestUser::query()->create([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => 'password',
            ]);
        }

        $results = $this
            ->service()
            ->paginate(10);

        $this->assertSame(
            30,
            $results->total()
        );

        $this->assertSame(
            10,
            $results->perPage()
        );

        $this->assertCount(
            10,
            $results->items()
        );
    }

    public function test_search_is_preserved(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        TestUser::query()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => 'password',
        ]);

        $query = new UserQuery(
            $this->app->make(
                UserManagementManager::class
            )
        );

        $service = new UserListService(
            $query->search('John'),
            new FakeAccountDriver(),
            new FakeUserAuthorizationManager()
        );

        $results = $service->paginate();

        $this->assertSame(
            1,
            $results->total()
        );

        $this->assertSame(
            'John Doe',
            $results->first()->displayName
        );
    }

    public function test_sorting_is_preserved(): void
    {
        TestUser::query()->create([
            'name' => 'Zoe',
            'email' => 'zoe@example.com',
            'password' => 'password',
        ]);

        TestUser::query()->create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'password',
        ]);

        $query = new UserQuery(
            $this->app->make(
                UserManagementManager::class
            )
        );

        $service = new UserListService(
            $query->sort('name', 'asc'),
            new FakeAccountDriver(),
            new FakeUserAuthorizationManager()
        );

        $results = $service->paginate();

        $this->assertSame(
            'Alice',
            $results->first()->displayName
        );

        $this->assertSame(
            'Zoe',
            $results->last()->displayName
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
    public bool $rolesCalled = false;

    public bool $permissionsCalled = false;

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
        $this->rolesCalled = true;

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
        $this->permissionsCalled = true;

        return $this->permissions;
    }
}

class TestUser extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];
}