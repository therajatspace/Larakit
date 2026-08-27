<?php

namespace Therajatspace\Larakit\Admin\Users\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Therajatspace\Larakit\Admin\Users\Detail\UserDetailService;
use Therajatspace\Larakit\Admin\Users\Listing\UserListService;

class UserController
{
    public function __construct(
        protected UserListService $userList,
        protected UserDetailService $userDetail
    ) {
    }

    public function index(): View
    {
        return view(
            'larakit::admin.users.index',
            [
                'users' => $this->userList->paginate(),
            ]
        );
    }

    public function show(
        mixed $id
    ): View {
        $user = $this->userDetail->find($id);

        abort_if(
            $user === null,
            404
        );

        return view(
            'larakit::admin.users.show',
            [
                'user' => $user,
            ]
        );
    }
}
