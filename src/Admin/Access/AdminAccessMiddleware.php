<?php

namespace Therajatspace\Larakit\Admin\Access;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccessMiddleware
{
    public function __construct(
        protected AdminAccessManager $manager
    ) {
    }

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (!auth()->check()) {
            abort(401);
        }

        if (!$this->manager->canAccess()) {
            abort(403);
        }

        return $next($request);
    }
}