<?php

namespace Therajatspace\Larakit\Auth\Password;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Therajatspace\Larakit\Auth\Contracts\PasswordConfirmationServiceContract;

class PasswordConfirmationService implements PasswordConfirmationServiceContract
{
    public function __construct(
        protected StatefulGuard $guard,
        protected Request $request,
    ) {
    }

    public function confirm(
        string $password
    ): void {
        if (! $this->guard->check()) {
            throw new RuntimeException(
                'Password confirmation requires an authenticated user.'
            );
        }

        $user = $this->guard->user();

        if ($user === null) {
            throw new RuntimeException(
                'Password confirmation requires an authenticated user.'
            );
        }

        $provider = $this->guard->getProvider();

        if (! $provider->validateCredentials(
            $user,
            ['password' => $password]
        )) {
            throw ValidationException::withMessages([
                'password' => [
                    'The provided password is incorrect.',
                ],
            ]);
        }

        $this->request
            ->session()
            ->passwordConfirmed();
    }
}
