<?php

namespace Tests\Unit\Admin\Users\Account;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use RuntimeException;
use Spatie\Permission\Traits\HasRoles;
use Therajatspace\Larakit\Admin\Users\Account\ConfigurableUserAccountDriver;

class ConfigurableUserAccountDriverTest extends TestCase
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
            'larakit.admin.users.account.status_attribute',
            'is_active'
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_active_user_is_reported_as_active(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'is_active' => true,
        ]);

        $driver = new ConfigurableUserAccountDriver();

        $this->assertTrue(
            $driver->isActive($user)
        );
    }

    public function test_inactive_user_is_reported_as_inactive(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'is_active' => false,
        ]);

        $driver = new ConfigurableUserAccountDriver();

        $this->assertFalse(
            $driver->isActive($user)
        );
    }

    public function test_activate_changes_account_state(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'is_active' => false,
        ]);

        $driver = new ConfigurableUserAccountDriver();

        $driver->activate($user);

        $user->refresh();

        $this->assertTrue(
            $user->is_active
        );
    }

    public function test_deactivate_changes_account_state(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'is_active' => true,
        ]);

        $driver = new ConfigurableUserAccountDriver();

        $driver->deactivate($user);

        $user->refresh();

        $this->assertFalse(
            $user->is_active
        );
    }

    public function test_status_attribute_can_be_customized(): void
    {
        config()->set(
            'larakit.admin.users.account.status_attribute',
            'enabled'
        );

        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'is_active' => true,
            'enabled' => false,
        ]);

        $driver = new ConfigurableUserAccountDriver();

        $this->assertFalse(
            $driver->isActive($user)
        );

        $driver->activate($user);

        $user->refresh();

        $this->assertTrue(
            $user->enabled
        );
    }

    public function test_missing_status_attribute_throws_exception(): void
    {
        config()->set(
            'larakit.admin.users.account.status_attribute',
            null
        );

        $user = new TestUser();

        $driver = new ConfigurableUserAccountDriver();

        $this->expectException(
            RuntimeException::class
        );

        $driver->isActive($user);
    }

    public function test_empty_status_attribute_throws_exception(): void
    {
        config()->set(
            'larakit.admin.users.account.status_attribute',
            '   '
        );

        $user = new TestUser();

        $driver = new ConfigurableUserAccountDriver();

        $this->expectException(
            RuntimeException::class
        );

        $driver->isActive($user);
    }

    public function test_invalid_status_attribute_configuration_throws_exception(): void
    {
        config()->set(
            'larakit.admin.users.account.status_attribute',
            ['is_active']
        );

        $user = new TestUser();

        $driver = new ConfigurableUserAccountDriver();

        $this->expectException(
            InvalidArgumentException::class
        );

        $driver->isActive($user);
    }

    public function test_missing_model_attribute_throws_exception(): void
    {
        config()->set(
            'larakit.admin.users.account.status_attribute',
            'does_not_exist'
        );

        $user = new TestUser();

        $driver = new ConfigurableUserAccountDriver();

        $this->expectException(
            InvalidArgumentException::class
        );

        $driver->isActive($user);
    }

    public function test_delete_removes_user(): void
    {
        $user = new DeleteTestUser();

        $user->name = 'John';
        $user->email = 'john@example.com';

        $user->save();

        $id = $user->getKey();

        $driver = new ConfigurableUserAccountDriver();

        $driver->delete($user);

        $this->assertDatabaseMissing(
            'users',
            [
                'id' => $id,
            ]
        );
    }

    public function test_non_eloquent_user_cannot_be_deleted(): void
    {
        $user = new NonEloquentUser();

        $driver = new ConfigurableUserAccountDriver();

        $this->expectException(
            InvalidArgumentException::class
        );

        $driver->delete($user);
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
            'enabled' => 'boolean',
        ];
    }
}

class DeleteTestUser extends Authenticatable
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

class NonAuthenticatableModel extends Model
{
    protected $table = 'users';
}