<?php

namespace Tests\Unit\Admin\Users\Password\State;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use RuntimeException;
use Therajatspace\Larakit\Admin\Users\Password\State\ConfigurableUserPasswordStateDriver;

class ConfigurableUserPasswordStateDriverTest extends TestCase
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
            'larakit.admin.users.password.force_change_attribute',
            'must_change_password'
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
                $table->boolean(
                    'must_change_password'
                )->default(false);
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

    public function test_user_is_not_required_to_change_password_by_default(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'must_change_password' => false,
        ]);

        $driver = new ConfigurableUserPasswordStateDriver();

        $this->assertFalse(
            $driver->mustChangePassword($user)
        );
    }

    public function test_force_password_change_marks_user(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'must_change_password' => false,
        ]);

        $driver = new ConfigurableUserPasswordStateDriver();

        $driver->forcePasswordChange($user);

        $user->refresh();

        $this->assertTrue(
            $user->must_change_password
        );
    }

    public function test_clear_forced_password_change_unmarks_user(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'must_change_password' => true,
        ]);

        $driver = new ConfigurableUserPasswordStateDriver();

        $driver->clearForcedPasswordChange($user);

        $user->refresh();

        $this->assertFalse(
            $user->must_change_password
        );
    }

    public function test_custom_attribute_is_supported(): void
    {
        Schema::connection('testing')->table(
            'users',
            function (Blueprint $table): void {
                $table->boolean(
                    'password_change_required'
                )->default(false);
            }
        );

        config()->set(
            'larakit.admin.users.password.force_change_attribute',
            'password_change_required'
        );

        $user = TestUser::query()->create([
            'name' => 'John',
            'must_change_password' => false,
            'password_change_required' => false,
        ]);

        $driver = new ConfigurableUserPasswordStateDriver();

        $driver->forcePasswordChange($user);

        $user->refresh();

        $this->assertTrue(
            $user->password_change_required
        );
    }

    public function test_missing_attribute_configuration_is_rejected(): void
    {
        config()->set(
            'larakit.admin.users.password.force_change_attribute',
            null
        );

        $user = new TestUser();

        $driver = new ConfigurableUserPasswordStateDriver();

        $this->expectException(
            RuntimeException::class
        );

        $driver->mustChangePassword($user);
    }

    public function test_empty_attribute_configuration_is_rejected(): void
    {
        config()->set(
            'larakit.admin.users.password.force_change_attribute',
            '   '
        );

        $user = new TestUser();

        $driver = new ConfigurableUserPasswordStateDriver();

        $this->expectException(
            RuntimeException::class
        );

        $driver->mustChangePassword($user);
    }

    public function test_invalid_attribute_configuration_is_rejected(): void
    {
        config()->set(
            'larakit.admin.users.password.force_change_attribute',
            ['must_change_password']
        );

        $user = new TestUser();

        $driver = new ConfigurableUserPasswordStateDriver();

        $this->expectException(
            InvalidArgumentException::class
        );

        $driver->mustChangePassword($user);
    }

    public function test_missing_model_attribute_is_rejected(): void
    {
        config()->set(
            'larakit.admin.users.password.force_change_attribute',
            'does_not_exist'
        );

        $user = new TestUser();

        $driver = new ConfigurableUserPasswordStateDriver();

        $this->expectException(
            InvalidArgumentException::class
        );

        $driver->mustChangePassword($user);
    }

    public function test_non_eloquent_user_is_rejected(): void
    {
        $user = new NonEloquentUser();

        $driver = new ConfigurableUserPasswordStateDriver();

        $this->expectException(
            InvalidArgumentException::class
        );

        $driver->forcePasswordChange($user);
    }
}

class TestUser extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'must_change_password' => 'boolean',
            'password_change_required' => 'boolean',
        ];
    }
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