<?php

namespace Therajatspace\Larakit\Admin\Users\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Therajatspace\Larakit\Admin\Users\UserManagementManager;

class UserQuery
{
    protected ?string $search = null;

    protected ?string $role = null;

    protected ?string $sort = null;

    protected ?string $direction = null;

    public function __construct(
        protected UserManagementManager $userManager
    ) {
    }

    public function search(?string $search): static
    {
        $search = $search === null
            ? null
            : trim($search);

        $this->search = $search === ''
            ? null
            : $search;

        return $this;
    }

    public function role(?string $role): static
    {
        $role = $role === null
            ? null
            : trim($role);

        $this->role = $role === ''
            ? null
            : $role;

        return $this;
    }

    public function sort(
        ?string $column,
        ?string $direction = null
    ): static {
        if ($column === null || trim($column) === '') {
            $this->sort = null;
            $this->direction = null;

            return $this;
        }

        $column = trim($column);

        $allowed = config(
            'larakit.admin.users.listing.sortable',
            []
        );

        if (!is_array($allowed)) {
            throw new InvalidArgumentException(
                'LaraKit Admin user sortable configuration must be an array.'
            );
        }

        if (!in_array($column, $allowed, true)) {
            throw new InvalidArgumentException(
                "User sorting column [{$column}] is not allowed."
            );
        }

        $direction = $direction === null
            ? config(
                'larakit.admin.users.listing.default_direction',
                'desc'
            )
            : strtolower(trim($direction));

        if (!in_array($direction, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException(
                'User sorting direction must be asc or desc.'
            );
        }

        $this->sort = $column;
        $this->direction = $direction;

        return $this;
    }

    public function query(): Builder
    {
        $model = $this->userManager->userModel();

        $instance = app($model);

        if (!$instance instanceof Model) {
            throw new InvalidArgumentException(
                'The configured user model must extend '
                . 'Illuminate\Database\Eloquent\Model.'
            );
        }

        $query = $instance->newQuery();

        $this->applySearch($query);
        $this->applyRole($query);
        $this->applySorting($query);

        return $query;
    }

    protected function applySearch(
        Builder $query
    ): void {
        if ($this->search === null) {
            return;
        }

        $searchable = config(
            'larakit.admin.users.listing.searchable',
            []
        );

        if (!is_array($searchable)) {
            throw new InvalidArgumentException(
                'LaraKit Admin user searchable configuration must be an array.'
            );
        }

        if ($searchable === []) {
            return;
        }

        foreach ($searchable as $column) {
            if (
                !is_string($column)
                || trim($column) === ''
            ) {
                throw new InvalidArgumentException(
                    'LaraKit Admin user searchable columns '
                    . 'must contain non-empty strings.'
                );
            }
        }

        $search = $this->search;

        $query->where(function (Builder $query) use ($searchable, $search): void {
            foreach ($searchable as $index => $column) {
                if ($index === 0) {
                    $query->where(
                        $column,
                        'like',
                        "%{$search}%"
                    );

                    continue;
                }

                $query->orWhere(
                    $column,
                    'like',
                    "%{$search}%"
                );
            }
        });
    }

    protected function applyRole(
        Builder $query
    ): void {
        if ($this->role === null) {
            return;
        }

        $model = $this->userManager->userModel();

        $instance = app($model);

        if (
            !method_exists(
                $instance,
                'roles'
            )
        ) {
            throw new InvalidArgumentException(
                'The configured user model does not support roles.'
            );
        }

        $query->whereHas(
            'roles',
            function (Builder $roleQuery): void {
                $roleQuery->where(
                    'name',
                    $this->role
                );
            }
        );
    }

    protected function applySorting(
        Builder $query
    ): void {
        $column = $this->sort;

        if ($column === null) {
            $column = config(
                'larakit.admin.users.listing.default_sort',
                'created_at'
            );
        }

        $direction = $this->direction;

        if ($direction === null) {
            $direction = config(
                'larakit.admin.users.listing.default_direction',
                'desc'
            );
        }

        if (!is_string($column) || trim($column) === '') {
            throw new InvalidArgumentException(
                'LaraKit Admin user default sort must be a non-empty string.'
            );
        }

        if (!is_string($direction)) {
            throw new InvalidArgumentException(
                'LaraKit Admin user default sort direction must be a string.'
            );
        }

        $direction = strtolower(
            trim($direction)
        );

        if (!in_array($direction, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException(
                'User sorting direction must be asc or desc.'
            );
        }

        $query->orderBy(
            $column,
            $direction
        );
    }

    public function paginate(
        ?int $perPage = null,
        ?int $page = null
    ) {
        $perPage ??= (int) config(
            'larakit.admin.users.listing.per_page',
            25
        );

        $maxPerPage = (int) config(
            'larakit.admin.users.listing.max_per_page',
            100
        );

        if ($perPage < 1) {
            throw new InvalidArgumentException(
                'User pagination per-page value must be greater than zero.'
            );
        }

        if ($maxPerPage < 1) {
            throw new InvalidArgumentException(
                'User pagination maximum per-page value must be greater than zero.'
            );
        }

        if ($perPage > $maxPerPage) {
            $perPage = $maxPerPage;
        }

        if ($page !== null && $page < 1) {
            throw new InvalidArgumentException(
                'User pagination page must be greater than zero.'
            );
        }

        return $this
            ->query()
            ->paginate(
                $perPage,
                ['*'],
                'page',
                $page
            );
    }
}