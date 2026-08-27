<?php

namespace Tests\Unit\Admin\Users\Queries;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\Users\Queries\UserQuery;
use Therajatspace\Larakit\Admin\Users\UserManagementManager;

class UserQueryTest extends TestCase
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
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsersTable();
    }

    protected function tearDown(): void
    {
        Schema::connection('testing')->dropIfExists(
            'users'
        );

        parent::tearDown();
    }

    protected function createUsersTable(): void
    {
        Schema::connection('testing')->create(
            'users',
            function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            }
        );
    }

    protected function userQuery(): UserQuery
    {
        /*
         * Resolve UserManagementManager through Laravel's container
         * because its constructor has required dependencies.
         */
        return new UserQuery(
            $this->app->make(
                UserManagementManager::class
            )
        );
    }

    public function test_query_returns_all_users_by_default(): void
    {
        TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        TestUser::query()->create([
            'name' => 'Jane',
            'email' => 'jane@example.com',
            'password' => 'password',
        ]);

        $users = $this
            ->userQuery()
            ->query()
            ->get();

        $this->assertCount(
            2,
            $users
        );
    }

    public function test_search_matches_name(): void
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

        $users = $this
            ->userQuery()
            ->search('John')
            ->query()
            ->get();

        $this->assertCount(
            1,
            $users
        );

        $this->assertSame(
            'John Doe',
            $users->first()->name
        );
    }

    public function test_search_matches_email(): void
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

        $users = $this
            ->userQuery()
            ->search('jane@example.com')
            ->query()
            ->get();

        $this->assertCount(
            1,
            $users
        );

        $this->assertSame(
            'Jane Smith',
            $users->first()->name
        );
    }

    public function test_search_is_case_insensitive_on_sqlite(): void
    {
        TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $users = $this
            ->userQuery()
            ->search('john')
            ->query()
            ->get();

        $this->assertCount(
            1,
            $users
        );
    }

    public function test_whitespace_only_search_is_ignored(): void
    {
        TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $users = $this
            ->userQuery()
            ->search('   ')
            ->query()
            ->get();

        $this->assertCount(
            1,
            $users
        );
    }

    public function test_role_filter_requires_role_support(): void
    {
        $query = $this
            ->userQuery()
            ->role('editor');

        $this->expectException(
            InvalidArgumentException::class
        );

        $query
            ->query()
            ->get();
    }

    public function test_invalid_sort_column_is_rejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this
            ->userQuery()
            ->sort('password');
    }

    public function test_invalid_sort_direction_is_rejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this
            ->userQuery()
            ->sort(
                'name',
                'sideways'
            );
    }

    public function test_sort_ascending(): void
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

        $users = $this
            ->userQuery()
            ->sort('name', 'asc')
            ->query()
            ->get();

        $this->assertSame(
            'Alice',
            $users->first()->name
        );

        $this->assertSame(
            'Zoe',
            $users->last()->name
        );
    }

    public function test_sort_descending(): void
    {
        TestUser::query()->create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'password',
        ]);

        TestUser::query()->create([
            'name' => 'Zoe',
            'email' => 'zoe@example.com',
            'password' => 'password',
        ]);

        $users = $this
            ->userQuery()
            ->sort('name', 'desc')
            ->query()
            ->get();

        $this->assertSame(
            'Zoe',
            $users->first()->name
        );

        $this->assertSame(
            'Alice',
            $users->last()->name
        );
    }

    public function test_default_sort_is_applied(): void
    {
        $firstCreatedAt = now()->subMinute();
        $secondCreatedAt = now();

        $first = TestUser::query()->create([
            'name' => 'First',
            'email' => 'first@example.com',
            'password' => 'password',
            'created_at' => $firstCreatedAt,
            'updated_at' => $firstCreatedAt,
        ]);

        $second = TestUser::query()->create([
            'name' => 'Second',
            'email' => 'second@example.com',
            'password' => 'password',
            'created_at' => $secondCreatedAt,
            'updated_at' => $secondCreatedAt,
        ]);

        $users = $this
            ->userQuery()
            ->query()
            ->get();

        $this->assertSame(
            $second->id,
            $users->first()->id
        );

        $this->assertSame(
            $first->id,
            $users->last()->id
        );
    }

    public function test_null_sort_uses_default_sort(): void
    {
        $users = $this
            ->userQuery()
            ->sort(null)
            ->query()
            ->get();

        $this->assertCount(
            0,
            $users
        );
    }

    public function test_invalid_searchable_configuration_is_rejected(): void
    {
        config()->set(
            'larakit.admin.users.listing.searchable',
            'name'
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $this
            ->userQuery()
            ->search('John')
            ->query()
            ->get();
    }

    public function test_invalid_searchable_column_is_rejected(): void
    {
        config()->set(
            'larakit.admin.users.listing.searchable',
            [
                'name',
                '',
            ]
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $this
            ->userQuery()
            ->search('John')
            ->query()
            ->get();
    }

    public function test_invalid_sortable_configuration_is_rejected(): void
    {
        config()->set(
            'larakit.admin.users.listing.sortable',
            'name'
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $this
            ->userQuery()
            ->sort('name');
    }

    public function test_default_pagination_is_applied(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            TestUser::query()->create([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => 'password',
            ]);
        }

        $results = $this
            ->userQuery()
            ->paginate();

        $this->assertSame(
            25,
            $results->perPage()
        );

        $this->assertSame(
            30,
            $results->total()
        );

        $this->assertCount(
            25,
            $results->items()
        );
    }

    public function test_custom_pagination_size_is_applied(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            TestUser::query()->create([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => 'password',
            ]);
        }

        $results = $this
            ->userQuery()
            ->paginate(10);

        $this->assertSame(
            10,
            $results->perPage()
        );

        $this->assertSame(
            30,
            $results->total()
        );

        $this->assertCount(
            10,
            $results->items()
        );
    }

    public function test_pagination_size_is_limited_to_configured_maximum(): void
    {
        config()->set(
            'larakit.admin.users.listing.max_per_page',
            50
        );

        for ($i = 1; $i <= 60; $i++) {
            TestUser::query()->create([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => 'password',
            ]);
        }

        $results = $this
            ->userQuery()
            ->paginate(1000);

        $this->assertSame(
            50,
            $results->perPage()
        );

        $this->assertCount(
            50,
            $results->items()
        );
    }

    public function test_zero_per_page_is_rejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this
            ->userQuery()
            ->paginate(0);
    }

    public function test_negative_per_page_is_rejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this
            ->userQuery()
            ->paginate(-10);
    }

    public function test_zero_page_is_rejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this
            ->userQuery()
            ->paginate(
                25,
                0
            );
    }

    public function test_negative_page_is_rejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this
            ->userQuery()
            ->paginate(
                25,
                -1
            );
    }

    public function test_pagination_works_with_search(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            TestUser::query()->create([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => 'password',
            ]);
        }

        TestUser::query()->create([
            'name' => 'Special User',
            'email' => 'special@example.com',
            'password' => 'password',
        ]);

        $results = $this
            ->userQuery()
            ->search('Special')
            ->paginate(25);

        $this->assertSame(
            1,
            $results->total()
        );

        $this->assertCount(
            1,
            $results->items()
        );

        $this->assertSame(
            'Special User',
            $results->first()->name
        );
    }

    public function test_pagination_works_with_sorting(): void
    {
        foreach (
            ['Zoe', 'Alice', 'John', 'Bob']
            as $name
        ) {
            TestUser::query()->create([
                'name' => $name,
                'email' => strtolower($name) . '@example.com',
                'password' => 'password',
            ]);
        }

        $results = $this
            ->userQuery()
            ->sort('name', 'asc')
            ->paginate(2);

        $this->assertSame(
            4,
            $results->total()
        );

        $this->assertCount(
            2,
            $results->items()
        );

        $this->assertSame(
            'Alice',
            $results->first()->name
        );

        $this->assertSame(
            'Bob',
            $results->last()->name
        );
    }
}

class TestUser extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];
}