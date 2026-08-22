<?php

namespace Therajatspace\Larakit\Auth\Contracts;

interface AuthContextContract
{
    public function guard(): string;

    public function provider(): string;

    public function userModel(): string;
}