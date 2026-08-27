<?php

namespace Tests\Unit\Admin\Users\Data;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\Users\Data\UserListData;

class UserListDataTest extends TestCase
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

    public function test_user_is_converted_to_list_data(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $data = UserListData::fromUser($user);

        $this->assertSame(
            $user->id,
            $data->id
        );

        $this->assertSame(
            'John Doe',
            $data->displayName
        );

        $this->assertSame(
            'john@example.com',
            $data->email
        );
    }

    public function test_nullable_email_is_supported(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => null,
            'password' => 'password',
        ]);

        $data = UserListData::fromUser($user);

        $this->assertNull(
            $data->email
        );
    }

    public function test_roles_are_empty_until_authorization_data_is_composed(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $data = UserListData::fromUser($user);

        $this->assertSame(
            [],
            $data->roles
        );
    }

    public function test_permissions_are_empty_until_authorization_data_is_composed(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $data = UserListData::fromUser($user);

        $this->assertSame(
            [],
            $data->permissions
        );
    }

    public function test_account_status_is_not_assumed(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $data = UserListData::fromUser($user);

        $this->assertNull(
            $data->accountStatus
        );
    }

    public function test_custom_display_name_attribute_is_supported(): void
    {
        config()->set(
            'larakit.admin.users.identity.display_name_attribute',
            'full_name'
        );

        Schema::connection('testing')->table(
            'users',
            function (Blueprint $table): void {
                $table->string('full_name')->nullable();
            }
        );

        $user = TestUser::query()->create([
            'name' => 'John',
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $data = UserListData::fromUser($user);

        $this->assertSame(
            'John Doe',
            $data->displayName
        );
    }

    public function test_custom_email_attribute_is_supported(): void
    {
        config()->set(
            'larakit.admin.users.identity.email_attribute',
            'email_address'
        );

        Schema::connection('testing')->table(
            'users',
            function (Blueprint $table): void {
                $table->string('email_address')->nullable();
            }
        );

        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'old@example.com',
            'email_address' => 'new@example.com',
            'password' => 'password',
        ]);

        $data = UserListData::fromUser($user);

        $this->assertSame(
            'new@example.com',
            $data->email
        );
    }

    public function test_invalid_display_name_configuration_is_rejected(): void
    {
        config()->set(
            'larakit.admin.users.identity.display_name_attribute',
            ''
        );

        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $this->expectException(
            InvalidArgumentException::class
        );

        UserListData::fromUser($user);
    }

    public function test_invalid_email_configuration_is_rejected(): void
    {
        config()->set(
            'larakit.admin.users.identity.email_attribute',
            []
        );

        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $this->expectException(
            InvalidArgumentException::class
        );

        UserListData::fromUser($user);
    }

    public function test_non_eloquent_user_is_rejected(): void
    {
        $user = new NonEloquentUser();

        $this->expectException(
            InvalidArgumentException::class
        );

        UserListData::fromUser($user);
    }
}

class TestUser extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];
}

class NonEloquentUser implements \Illuminate\Contracts\Auth\Authenticatable
{
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return null;
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
}