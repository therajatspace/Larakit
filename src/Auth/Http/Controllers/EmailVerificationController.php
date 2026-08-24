<?php

namespace Therajatspace\Larakit\Auth\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Therajatspace\Larakit\Auth\Contracts\EmailVerificationServiceContract;

class EmailVerificationController
{
    public function __construct(
        protected EmailVerificationServiceContract $verification
    ) {
    }

    public function verify(
        Request $request,
        string $id,
        string $hash
    ): Response {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired verification link.');
        }

        $user = Auth::user();

        if (! $user instanceof MustVerifyEmail) {
            abort(403, 'The authenticated user cannot be email verified.');
        }

        if ((string) $user->getKey() !== (string) $id) {
            abort(403, 'Invalid verification user.');
        }

        if (! hash_equals(
            sha1($user->getEmailForVerification()),
            $hash
        )) {
            abort(403, 'Invalid verification hash.');
        }

        $this->verification->verify($user);

        return response()->noContent();
    }
}
