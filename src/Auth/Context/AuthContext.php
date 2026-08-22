<?php

namespace Therajatspace\Larakit\Auth\Context;

use Therajatspace\Larakit\Auth\Contracts\AuthContextContract;
use Therajatspace\Larakit\Auth\Contracts\UserResolverContract;

class AuthContext implements AuthContextContract
{
    public function __construct(
        protected UserResolverContract $userResolver
    ) {
    }

    public function guard(): string
    {
        return $this->userResolver->guard();
    }

    public function provider(): string
    {
        return $this->userResolver->provider();
    }

    public function userModel(): string
    {
        return $this->userResolver->model();
    }
}