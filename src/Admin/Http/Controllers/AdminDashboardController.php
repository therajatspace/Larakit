<?php

namespace Therajatspace\Larakit\Admin\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('larakit::admin.dashboard.index');
    }
}