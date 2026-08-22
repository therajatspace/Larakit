<?php

namespace Therajatspace\Larakit\Auth\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Therajatspace\Larakit\Auth\Registration\RegistrationData;
use Therajatspace\Larakit\Auth\Registration\RegistrationService;

class RegistrationController
{
    public function __construct(
        protected RegistrationService $registration
    ) {
    }

    public function store(
        Request $request
    ): JsonResponse|RedirectResponse {
        $data = RegistrationData::make(
            name: (string) $request->input('name'),
            email: (string) $request->input('email'),
            password: (string) $request->input('password'),
            profile: $request->input('profile')
                !== null
                ? (string) $request->input('profile')
                : null,
        );

        /** @var Authenticatable $user */
        $user = $this->registration->register($data);

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Registered successfully.',
                'user' => $user,
            ], 201);
        }

        return redirect('/');
    }
}