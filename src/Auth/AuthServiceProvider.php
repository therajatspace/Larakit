<?php

namespace Therajatspace\Larakit\Auth;

use Illuminate\Support\ServiceProvider;
use Therajatspace\Larakit\Auth\Context\AuthContext;
use Therajatspace\Larakit\Auth\Contracts\AuthContextContract;
use Therajatspace\Larakit\Auth\Contracts\AuthManagerContract;
use Therajatspace\Larakit\Auth\Contracts\UserResolverContract;
use Therajatspace\Larakit\Auth\Profiles\ProfileManager;
use Therajatspace\Larakit\Auth\Users\UserResolver;
use Therajatspace\Larakit\Auth\Registration\RegistrationValidator;
use Therajatspace\Larakit\Auth\Registration\RegistrationService;
use Therajatspace\Larakit\Auth\Authorization\AuthorizationManager;
use Therajatspace\Larakit\Auth\Contracts\AuthorizationManagerContract;
use Therajatspace\Larakit\Auth\Authorization\RoleManager;
use Therajatspace\Larakit\Auth\Contracts\RoleManagerContract;
use Therajatspace\Larakit\Auth\Authorization\PermissionManager;
use Therajatspace\Larakit\Auth\Contracts\PermissionManagerContract;
use Therajatspace\Larakit\Auth\Authorization\UserAuthorizationManager;
use Therajatspace\Larakit\Auth\Contracts\UserAuthorizationManagerContract;
use Therajatspace\Larakit\Auth\Authorization\AuthorizationGate;
use Therajatspace\Larakit\Auth\Contracts\AuthorizationGateContract;
use Therajatspace\Larakit\Auth\Authorization\DelegationManager;
use Therajatspace\Larakit\Auth\Contracts\DelegationManagerContract;
use Therajatspace\Larakit\Auth\Authorization\DelegationConfig;
use Therajatspace\Larakit\Auth\Contracts\DelegationConfigContract;
use Therajatspace\Larakit\Auth\Authorization\DelegationService;
use Therajatspace\Larakit\Auth\Contracts\DelegationServiceContract;

use Therajatspace\Larakit\Auth\Contracts\LoginServiceContract;
use Therajatspace\Larakit\Auth\Login\LoginService;
use Therajatspace\Larakit\Auth\Login\LoginValidator;
use Therajatspace\Larakit\Auth\Login\LoginRateLimiter;
use Therajatspace\Larakit\Auth\Contracts\LoginRateLimiterContract;

use Therajatspace\Larakit\Auth\AuthRouteRegistrar;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            AuthManagerContract::class,
            AuthManager::class
        );

        $this->app->singleton(
            AuthManager::class
        );

        $this->app->singleton(
            ProfileManager::class,
            function () {
                return new ProfileManager(
                    config('larakit.auth.profiles', [])
                );
            }
        );

        $this->app->singleton(
            UserResolverContract::class,
            UserResolver::class
        );

        $this->app->singleton(
            UserResolver::class
        );

        $this->app->singleton(
            AuthContextContract::class,
            AuthContext::class
        );

        $this->app->singleton(
            AuthContext::class
        );

        $this->app->singleton(
            RegistrationValidator::class
        );

        $this->app->singleton(
            RegistrationService::class
        );

        $this->app->singleton(
            AuthorizationManagerContract::class,
            AuthorizationManager::class
        );

        $this->app->singleton(
            AuthorizationManager::class
        );

        $this->app->singleton(
            RoleManagerContract::class,
            RoleManager::class
        );

        $this->app->singleton(
            RoleManager::class
        );

        $this->app->singleton(
            PermissionManagerContract::class,
            PermissionManager::class
        );

        $this->app->singleton(
            PermissionManager::class
        );

        $this->app->singleton(
            UserAuthorizationManagerContract::class,
            UserAuthorizationManager::class
        );

        $this->app->singleton(
            UserAuthorizationManager::class
        );

        $this->app->singleton(
            AuthorizationGateContract::class,
            AuthorizationGate::class
        );

        $this->app->singleton(
            AuthorizationGate::class
        );

        $this->app->singleton(
            DelegationManagerContract::class,
            DelegationManager::class
        );

        $this->app->singleton(
            DelegationManager::class
        );

        $this->app->singleton(
            DelegationConfigContract::class,
            DelegationConfig::class
        );

        $this->app->singleton(
            DelegationConfig::class
        );

        $this->app->singleton(
            DelegationServiceContract::class,
            DelegationService::class
        );

        $this->app->singleton(
            DelegationService::class
        );


        $this->app->singleton(
            LoginValidator::class
        );

        $this->app->singleton(
            LoginServiceContract::class,
            LoginService::class
        );

        $this->app->singleton(
            LoginService::class
        );

        $this->app->singleton(
            LoginRateLimiterContract::class,
            LoginRateLimiter::class
        );

        $this->app->singleton(
            LoginRateLimiter::class
        );


        $this->app->singleton(
            AuthRouteRegistrar::class
        );
    }

}