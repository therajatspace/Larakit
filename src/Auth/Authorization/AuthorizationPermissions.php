<?php

namespace Therajatspace\Larakit\Auth\Authorization;

final class AuthorizationPermissions
{
    public const USERS_VIEW = 'users.view';
    public const USERS_CREATE = 'users.create';
    public const USERS_UPDATE = 'users.update';
    public const USERS_DELETE = 'users.delete';
    public const USERS_MANAGE = 'users.manage';

    public const ROLES_VIEW = 'roles.view';
    public const ROLES_CREATE = 'roles.create';
    public const ROLES_UPDATE = 'roles.update';
    public const ROLES_DELETE = 'roles.delete';
    public const ROLES_ASSIGN = 'roles.assign';
    public const ROLES_MANAGE = 'roles.manage';

    public const PERMISSIONS_VIEW = 'permissions.view';
    public const PERMISSIONS_CREATE = 'permissions.create';
    public const PERMISSIONS_UPDATE = 'permissions.update';
    public const PERMISSIONS_DELETE = 'permissions.delete';
    public const PERMISSIONS_ASSIGN = 'permissions.assign';
    public const PERMISSIONS_MANAGE = 'permissions.manage';

    public static function all(): array
    {
        return [
            self::USERS_VIEW,
            self::USERS_CREATE,
            self::USERS_UPDATE,
            self::USERS_DELETE,
            self::USERS_MANAGE,

            self::ROLES_VIEW,
            self::ROLES_CREATE,
            self::ROLES_UPDATE,
            self::ROLES_DELETE,
            self::ROLES_ASSIGN,
            self::ROLES_MANAGE,

            self::PERMISSIONS_VIEW,
            self::PERMISSIONS_CREATE,
            self::PERMISSIONS_UPDATE,
            self::PERMISSIONS_DELETE,
            self::PERMISSIONS_ASSIGN,
            self::PERMISSIONS_MANAGE,
        ];
    }

    private function __construct()
    {
    }
}