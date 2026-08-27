<?php

namespace Therajatspace\Larakit\Admin\Contracts;

interface AdminAccessManagerContract
{
    public function permission(): ?string;

    public function canAccess(): bool;
}