<?php

namespace Therajatspace\Larakit\Admin\Users\Listing;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use InvalidArgumentException;
use Therajatspace\Larakit\Admin\Users\Contracts\UserAccountDriverContract;
use Therajatspace\Larakit\Admin\Users\Data\UserListData;
use Therajatspace\Larakit\Admin\Users\Queries\UserQuery;
use Therajatspace\Larakit\Auth\Contracts\UserAuthorizationManagerContract;

class UserListService
{
    public function __construct(
        protected UserQuery $userQuery,
        protected UserAccountDriverContract $accountDriver,
        protected UserAuthorizationManagerContract $authorization
    ) {
    }

    public function paginate(
        ?int $perPage = null,
        ?int $page = null
    ): LengthAwarePaginator {
        $paginator = $this->userQuery->paginate(
            $perPage,
            $page
        );

        $paginator->setCollection(
            $paginator
                ->getCollection()
                ->map(
                    function ($user): UserListData {
                        if (
                            !$user instanceof Authenticatable
                        ) {
                            throw new InvalidArgumentException(
                                'User query results must implement '
                                . 'Illuminate\Contracts\Auth\Authenticatable.'
                            );
                        }

                        $data = UserListData::fromUser(
                            $user
                        );

                        $data = $data->withAccountStatus(
                            $this->accountDriver->isActive(
                                $user
                            )
                        );

                        if (
                            $this->authorizationSupportsRoles(
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
                            $this->authorizationSupportsPermissions(
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
                )
        );

        return $paginator;
    }

    protected function authorizationSupportsRoles(
        Authenticatable $user
    ): bool {
        return $this->authorization->supportsRoles(
            $user
        );
    }

    protected function authorizationSupportsPermissions(
        Authenticatable $user
    ): bool {
        return $this->authorization->supportsPermissions(
            $user
        );
    }
}