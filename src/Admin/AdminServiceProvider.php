<?php

namespace Therajatspace\Larakit\Admin;

use Illuminate\Support\ServiceProvider;
use Therajatspace\Larakit\Admin\Access\AdminAccessManager;
use Therajatspace\Larakit\Admin\Contracts\AdminAccessManagerContract;
use Therajatspace\Larakit\Admin\Contracts\AdminManagerContract;
use Therajatspace\Larakit\Admin\Users\UserManagementManager;
use Therajatspace\Larakit\Admin\Users\Contracts\UserManagementManagerContract;
use Therajatspace\Larakit\Admin\Users\Account\ConfigurableUserAccountDriver;
use Therajatspace\Larakit\Admin\Users\Contracts\UserAccountDriverContract;
use Therajatspace\Larakit\Admin\Users\Contracts\UserPasswordManagerContract;
use Therajatspace\Larakit\Admin\Users\Password\UserPasswordManager;
use Therajatspace\Larakit\Admin\Users\Contracts\UserPasswordStateDriverContract;
use Therajatspace\Larakit\Admin\Users\Password\State\ConfigurableUserPasswordStateDriver;
use Therajatspace\Larakit\Admin\Users\Account\UserAccountManager;
use Therajatspace\Larakit\Admin\Users\Contracts\UserAccountManagerContract;

class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            AdminManagerContract::class,
            AdminManager::class
        );

        $this->app->singleton(
            AdminManager::class
        );

        $this->app->singleton(
            AdminAccessManagerContract::class,
            AdminAccessManager::class
        );

        $this->app->singleton(
            AdminAccessManager::class
        );

        $this->app->singleton(
            AdminRouteRegistrar::class
        );

        $this->app->singleton(
            UserManagementManagerContract::class,
            UserManagementManager::class
        );

        $this->app->singleton(
            UserManagementManager::class
        );

        $this->app->singleton(
            UserAccountDriverContract::class,
            function ($app) {
                $driver = config(
                    'larakit.admin.users.account.driver'
                );

                if ($driver === null) {
                    return $app->make(
                        ConfigurableUserAccountDriver::class
                    );
                }

                return $app->make($driver);
            }
        );

        $this->app->singleton(
            ConfigurableUserAccountDriver::class
        );

        $this->app->singleton(
            UserPasswordManagerContract::class,
            UserPasswordManager::class
        );

        $this->app->singleton(
            UserPasswordManager::class
        );

        $this->app->singleton(
            UserPasswordStateDriverContract::class,
            function ($app) {
                $driver = config(
                    'larakit.admin.users.password.state_driver'
                );

                if ($driver === null) {
                    return $app->make(
                        ConfigurableUserPasswordStateDriver::class
                    );
                }

                return $app->make($driver);
            }
        );

        $this->app->singleton(
            ConfigurableUserPasswordStateDriver::class
        );

        $this->app->singleton(
            UserAccountManagerContract::class,
            UserAccountManager::class
        );

        $this->app->singleton(
            UserAccountManager::class
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__ . '/../../resources/views',
            'larakit'
        );

        $this->app
            ->make(AdminRouteRegistrar::class)
            ->register();
    }
}