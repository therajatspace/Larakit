<?php

namespace Therajatspace\Larakit\Auth\Http\Controllers;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Therajatspace\Larakit\Auth\Contracts\EmailVerificationServiceContract;

class EmailVerificationNotificationController
{
    public function __construct(
        protected EmailVerificationServiceContract $verification
    ) {
    }

    public function store(
        Request $request
    ): JsonResponse {
        $user = $request->user();

        if (! $user instanceof MustVerifyEmail) {
            abort(
                403,
                'The authenticated user cannot use email verification.'
            );
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email address is already verified.',
            ]);
        }

        $this->verification->send($user);

        return response()->json([
            'message' => 'Verification link sent.',
        ]);
    }
}
