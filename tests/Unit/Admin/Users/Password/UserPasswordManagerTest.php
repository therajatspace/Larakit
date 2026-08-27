<?php

namespace Tests\Unit\Admin\Users\Password;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\Users\Password\UserPasswordManager;

class UserPasswordManagerTest extends TestCase
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
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsersTable();
        $this->createPasswordlessUsersTable();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('passwordless_users');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    protected function createUsersTable(): void
    {
        Schema::connection('testing')->create(
            'users',
            function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            }
        );
    }

    protected function createPasswordlessUsersTable(): void
    {
        Schema::connection('testing')->create(
            'passwordless_users',
            function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->timestamps();
            }
        );
    }

    public function test_password_is_hashed(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $manager = new UserPasswordManager();

        $manager->setPassword(
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
    }

    public function test_old_password_no_longer_works(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $manager = new UserPasswordManager();

        $manager->setPassword(
            $user,
            'New-password123!'
        );

        $user->refresh();

        $this->assertFalse(
            Hash::check(
                'old-password',
                $user->password
            )
        );
    }

    public function test_password_is_saved(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $manager = new UserPasswordManager();

        $manager->setPassword(
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
    }

    public function test_remember_token_is_rotated(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => Hash::make('old-password'),
            'remember_token' => 'old-token',
        ]);

        $manager = new UserPasswordManager();

        $manager->setPassword(
            $user,
            'New-password123!'
        );

        $user->refresh();

        $this->assertNotSame(
            'old-token',
            $user->remember_token
        );

        $this->assertNotNull(
            $user->remember_token
        );

        $this->assertSame(
            60,
            strlen($user->remember_token)
        );
    }

    public function test_password_confirmation_can_be_explicitly_supplied(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $manager = new UserPasswordManager();

        $manager->setPassword(
            $user,
            'New-password123!',
            'New-password123!'
        );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                'New-password123!',
                $user->password
            )
        );
    }

    public function test_mismatched_password_confirmation_is_rejected(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $manager = new UserPasswordManager();

        $this->expectException(
            ValidationException::class
        );

        $manager->setPassword(
            $user,
            'New-password123!',
            'Different-password123!'
        );
    }

    public function test_empty_password_is_rejected(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $manager = new UserPasswordManager();

        $this->expectException(
            ValidationException::class
        );

        $manager->setPassword(
            $user,
            ''
        );
    }

    public function test_weak_password_is_rejected_by_default_password_rules(): void
    {
        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $manager = new UserPasswordManager();

        $this->expectException(
            ValidationException::class
        );

        $manager->setPassword(
            $user,
            '123'
        );
    }

    public function test_non_eloquent_user_is_rejected(): void
    {
        $user = new NonEloquentUser();

        $manager = new UserPasswordManager();

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->setPassword(
            $user,
            'New-password123!'
        );
    }

    public function test_user_without_password_attribute_is_rejected(): void
    {
        $user = PasswordlessUser::query()->create([
            'name' => 'John',
        ]);

        $manager = new UserPasswordManager();

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->setPassword(
            $user,
            'New-password123!'
        );
    }

    public function test_password_is_never_stored_as_plain_text(): void
    {
        $plainTextPassword = 'New-password123!';

        $user = TestUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $manager = new UserPasswordManager();

        $manager->setPassword(
            $user,
            $plainTextPassword
        );

        $user->refresh();

        $this->assertNotSame(
            $plainTextPassword,
            $user->password
        );
    }
}

class TestUser extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];
}

class PasswordlessUser extends Authenticatable
{
    protected $table = 'passwordless_users';

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