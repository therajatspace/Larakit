<?php

namespace Therajatspace\Larakit\Admin\Users\Detail;

use Illuminate\Contracts\Auth\Authenticatable;
use InvalidArgumentException;
use Therajatspace\Larakit\Admin\Users\Contracts\UserAccountDriverContract;
use Therajatspace\Larakit\Admin\Users\Data\UserDetailData;
use Therajatspace\Larakit\Admin\Users\UserManagementManager;
use Therajatspace\Larakit\Auth\Contracts\UserAuthorizationManagerContract;

class UserDetailService
{
    public function __construct(
        protected UserManagementManager $userManager,
        protected UserAccountDriverContract $accountDriver,
        protected UserAuthorizationManagerContract $authorization
    ) {
    }

    public function find(
        mixed $id
    ): ?UserDetailData {
        $user = $this->userManager->find($id);

        if ($user === null) {
            return null;
        }

        return $this->fromUser($user);
    }

    public function fromUser(
        Authenticatable $user
    ): UserDetailData {
        $data = UserDetailData::fromUser(
            $user
        );

        $data = $data->withAccountStatus(
            $this->accountDriver->isActive(
                $user
            )
        );

        if (
            $this->authorization->supportsRoles(
                $user
            )
        ) {
            $data = $data->withRoles(
                $this->authorization->roles(
                    $user
                )
            );
        }

        if (
            $this->authorization->supportsPermissions(
                $user
            )
        ) {
            $data = $data->withPermissions(
                $this->authorization->permissions(
                    $user
                )
            );
        }

        return $data;
    }
}
