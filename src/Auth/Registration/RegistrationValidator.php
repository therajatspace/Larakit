<?php

namespace Therajatspace\Larakit\Auth\Registration;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Therajatspace\Larakit\Auth\Profiles\AuthProfile;
use Therajatspace\Larakit\Auth\Profiles\ProfileManager;

class RegistrationValidator
{
    public function __construct(
        protected ProfileManager $profiles,
    ) {
    }

    /**
     * Validate registration data and resolve the requested profile.
     *
     * @throws ValidationException
     */
    public function validate(
        RegistrationData $data
    ): ?AuthProfile {
        $validator = Validator::make(
            [
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
            ],
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                ],

                'password' => [
                    'required',
                    'string',
                    Password::defaults(),
                ],
            ]
        );

        $validator->validate();

        return $this->validateProfile($data);
    }

    protected function validateProfile(
        RegistrationData $data
    ): ?AuthProfile {
        if ($data->profile === null) {
            return null;
        }

        $profile = $this->profiles->find(
            $data->profile
        );

        if ($profile === null) {
            throw ValidationException::withMessages([
                'profile' => [
                    "Authentication profile [{$data->profile}] does not exist.",
                ],
            ]);
        }

        if (! $profile->registrationEnabled()) {
            throw ValidationException::withMessages([
                'profile' => [
                    "Registration is disabled for profile [{$profile->name()}].",
                ],
            ]);
        }

        return $profile;
    }
}