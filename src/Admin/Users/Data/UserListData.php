<?php

namespace Therajatspace\Larakit\Admin\Users\Data;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class UserListData
{
    public function __construct(
        public readonly mixed $id,
        public readonly string $displayName,
        public readonly ?string $email,
        public readonly mixed $accountStatus,
        public readonly array $roles,
        public readonly array $permissions,
        public readonly mixed $createdAt,
        public readonly mixed $updatedAt,
    ) {
    }

    public static function fromUser(
        Authenticatable $user
    ): static {
        if (!$user instanceof Model) {
            throw new InvalidArgumentException(
                'The configured user model must extend '
                . 'Illuminate\Database\Eloquent\Model.'
            );
        }

        $id = $user->getAuthIdentifier();

        if ($id === null) {
            throw new InvalidArgumentException(
                'The user must have an authentication identifier.'
            );
        }

        $displayNameAttribute = config(
            'larakit.admin.users.identity.display_name_attribute',
            'name'
        );

        if (
            !is_string($displayNameAttribute)
            || trim($displayNameAttribute) === ''
        ) {
            throw new InvalidArgumentException(
                'LaraKit Admin user display-name attribute '
                . 'must be a non-empty string.'
            );
        }

        $emailAttribute = config(
            'larakit.admin.users.identity.email_attribute',
            'email'
        );

        if (
            !is_string($emailAttribute)
            || trim($emailAttribute) === ''
        ) {
            throw new InvalidArgumentException(
                'LaraKit Admin user email attribute '
                . 'must be a non-empty string.'
            );
        }

        return new static(
            id: $id,
            displayName: (string) $user->getAttribute(
                $displayNameAttribute
            ),
            email: self::nullableString(
                $user->getAttribute($emailAttribute)
            ),
            accountStatus: null,
            roles: [],
            permissions: [],
            createdAt: $user->getAttribute('created_at'),
            updatedAt: $user->getAttribute('updated_at'),
        );
    }

    public function withAccountStatus(
        bool $accountStatus
    ): static {
        return new static(
            id: $this->id,
            displayName: $this->displayName,
            email: $this->email,
            accountStatus: $accountStatus,
            roles: $this->roles,
            permissions: $this->permissions,
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
        );
    }

    public function withRoles(
        array $roles
    ): static {
        return new static(
            id: $this->id,
            displayName: $this->displayName,
            email: $this->email,
            accountStatus: $this->accountStatus,
            roles: self::normalizeList($roles),
            permissions: $this->permissions,
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
        );
    }

    public function withPermissions(
        array $permissions
    ): static {
        return new static(
            id: $this->id,
            displayName: $this->displayName,
            email: $this->email,
            accountStatus: $this->accountStatus,
            roles: $this->roles,
            permissions: self::normalizeList($permissions),
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
        );
    }

    protected static function normalizeList(
        array $values
    ): array {
        return array_values(
            array_unique(
                array_filter(
                    array_map(
                        static function ($value): string {
                            return trim(
                                (string) $value
                            );
                        },
                        $values
                    ),
                    static function (string $value): bool {
                        return $value !== '';
                    }
                )
            )
        );
    }

    protected static function nullableString(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        return (string) $value;
    }
}