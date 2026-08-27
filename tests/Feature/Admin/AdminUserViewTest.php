<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\AdminServiceProvider;
use Therajatspace\Larakit\LaraKitServiceProvider;

class AdminUserViewTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
            AdminServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set(
            'app.key',
            'base64:' . base64_encode(
                random_bytes(32)
            )
        );

        $app['config']->set(
            'larakit.admin.enabled',
            true
        );

        $app['config']->set(
            'larakit.admin.route_prefix',
            'admin'
        );

        $app['config']->set(
            'larakit.admin.middleware',
            []
        );

        $app['config']->set(
            'larakit.admin.access.permission',
            'admin.access'
        );

        $app['config']->set(
            'larakit.admin.modules.dashboard',
            true
        );

        $app['config']->set(
            'larakit.admin.modules.users',
            true
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

        $app['config']->set(
            'larakit.admin.users.account.status_attribute',
            'is_active'
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create(
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

        $this->authenticate();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists(
            'users'
        );

        parent::tearDown();
    }

    protected function authenticate(): void
    {
        $user = new class extends Authenticatable {
            public function can(
                $ability,
                $arguments = []
            ) {
                return $ability === 'admin.access';
            }
        };

        $this->be($user);
    }

    public function test_users_index_renders_user_list(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);

        $response = $this->get(
            route('larakit.admin.users.index')
        );

        $response->assertOk();

        $response->assertSee(
            'Users'
        );

        $response->assertSee(
            'John Doe'
        );

        $response->assertSee(
            'john@example.com'
        );

        $response->assertSee(
            'Active'
        );
    }

    public function test_users_index_renders_search_form(): void
    {
        $response = $this->get(
            route('larakit.admin.users.index')
        );

        $response->assertOk();

        $response->assertSee(
            'Search users...'
        );

        $response->assertSee(
            'Search'
        );
    }

    public function test_users_index_renders_empty_state(): void
    {
        $response = $this->get(
            route('larakit.admin.users.index')
        );

        $response->assertOk();

        $response->assertSee(
            'No users found.'
        );
    }

    public function test_users_index_links_to_user_details(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);

        $response = $this->get(
            route('larakit.admin.users.index')
        );

        $response->assertOk();

        $response->assertSee(
            route(
                'larakit.admin.users.show',
                ['id' => $user->id]
            )
        );
    }

    public function test_user_show_renders_user_details(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);

        $response = $this->get(
            route(
                'larakit.admin.users.show',
                ['id' => $user->id]
            )
        );

        $response->assertOk();

        $response->assertSee(
            'John Doe'
        );

        $response->assertSee(
            'john@example.com'
        );

        $response->assertSee(
            'Active'
        );

        $response->assertSee(
            'Roles'
        );

        $response->assertSee(
            'Permissions'
        );
    }
}

class TestUser extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];
}