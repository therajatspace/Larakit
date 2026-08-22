<?php

namespace Therajatspace\Larakit\Auth\Contracts;

interface UserResolverContract
{
    public function guard(): string;

    public function provider(): string;

    public function model(): string;
}