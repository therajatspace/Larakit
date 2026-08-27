<?php

namespace Therajatspace\Larakit\Admin\Users\Account;

use Illuminate\Contracts\Auth\Authenticatable;
use Therajatspace\Larakit\Admin\Users\Contracts\UserAccountDriverContract;
use Therajatspace\Larakit\Admin\Users\Contracts\UserAccountManagerContract;

class UserAccountManager implements UserAccountManagerContract
{
    public function __construct(
        protected UserAccountDriverContract $driver
    ) {
    }

    public function activate(
        Authenticatable $user
    ): void {
        $this->driver->activate($user);
    }

    public function deactivate(
        Authenticatable $user
    ): void {
        $this->driver->deactivate($user);
    }

    public function delete(
        Authenticatable $user
    ): void {
        $this->driver->delete($user);
    }
}
