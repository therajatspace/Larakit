<?php

namespace Therajatspace\Larakit\Auth\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Therajatspace\Larakit\Auth\Contracts\LoginServiceContract;
use Therajatspace\Larakit\Auth\Login\LoginData;

class LoginController
{
    public function __construct(
        protected LoginServiceContract $loginService,
    ) {
    }

    public function store(
        Request $request
    ): JsonResponse|RedirectResponse {
        $data = LoginData::make(
            email: (string) $request->input('email'),
            password: (string) $request->input('password'),
            remember: $request->boolean('remember'),
        );

        $user = $this->loginService->login(
            $data
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Authenticated successfully.',
                'user' => $user,
            ]);
        }

        return redirect()->intended('/');
    }

    public function destroy(
        Request $request
    ): JsonResponse|RedirectResponse {
        $this->loginService->logout();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logged out successfully.',
            ]);
        }

        return redirect('/');
    }
}