<?php

namespace Therajatspace\Larakit\Auth\Registration;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Therajatspace\Larakit\Auth\Profiles\AuthProfile;
use Therajatspace\Larakit\Auth\Context\AuthContext;
use Therajatspace\Larakit\Auth\Contracts\UserAuthorizationManagerContract;

class RegistrationService
{
    public function __construct(
        protected AuthContext $context,
        protected RegistrationValidator $validator,
        protected UserAuthorizationManagerContract $authorization,
    ) {
    }

    public function register(
        RegistrationData $data
    ): Authenticatable {
        $profile = $this->validator->validate($data);

        return DB::transaction(function () use ($data, $profile) {
            $modelClass = $this->context->userModel();

            /** @var Model&Authenticatable $user */
            $user = new $modelClass();

            $attributes = config(
                'larakit.auth.user.attributes',
                []
            );

            $nameAttribute = $attributes['name'] ?? 'name';
            $emailAttribute = $attributes['email'] ?? 'email';
            $passwordAttribute = $attributes['password'] ?? 'password';

            $email = $data->email;

            $this->ensureEmailIsAvailable(
                $user,
                $emailAttribute,
                $email
            );

            $user->setAttribute(
                $nameAttribute,
                $data->name
            );

            $user->setAttribute(
                $emailAttribute,
                $email
            );

            $user->setAttribute(
                $passwordAttribute,
                Hash::make($data->password)
            );

            $user->save();

            $this->assignProfileRole(
                $user,
                $profile
            );

            return $user;
        });
    }

    protected function assignProfileRole(
        Authenticatable $user,
        ?AuthProfile $profile
    ): void {
        if ($profile === null) {
            return;
        }

        $role = $profile->role();

        if ($role === null) {
            return;
        }

        $this->authorization->assignRole(
            $user,
            $role
        );
    }

    protected function ensureEmailIsAvailable(
        Model $user,
        string $emailAttribute,
        string $email
    ): void {
        $exists = $user->newQuery()
            ->where($emailAttribute, $email)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'email' => [
                    'The email address is already registered.',
                ],
            ]);
        }
    }
}