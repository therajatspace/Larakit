<?php

namespace Therajatspace\Larakit\Admin;

use Illuminate\Support\Facades\Route;
use Therajatspace\Larakit\Admin\Access\AdminAccessMiddleware;
use Therajatspace\Larakit\Admin\Http\Controllers\AdminDashboardController;
use Therajatspace\Larakit\Admin\Users\Http\Controllers\UserController;

class AdminRouteRegistrar
{
    public function __construct(
        protected AdminManager $manager
    ) {
    }

    public function register(): void
    {
        if (!$this->manager->enabled()) {
            return;
        }

        Route::prefix(
            $this->manager->routePrefix()
        )
            ->middleware([
                ...$this->manager->middleware(),
                AdminAccessMiddleware::class,
            ])
            ->group(function (): void {
                if (
                    $this->manager->moduleEnabled(
                        'dashboard'
                    )
                ) {
                    Route::get(
                        '/',
                        [
                            AdminDashboardController::class,
                            'index',
                        ]
                    )->name(
                        'larakit.admin.dashboard'
                    );
                }

                if (
                    $this->manager->moduleEnabled(
                        'users'
                    )
                ) {
                    Route::get(
                        '/users',
                        [
                            UserController::class,
                            'index',
                        ]
                    )->name(
                        'larakit.admin.users.index'
                    );

                    Route::get(
                        '/users/{id}',
                        [
                            UserController::class,
                            'show',
                        ]
                    )->name(
                        'larakit.admin.users.show'
                    );
                }
            });
    }
}
