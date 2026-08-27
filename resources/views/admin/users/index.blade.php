@extends('larakit::admin.layouts.app')

@section('title', 'Users')

@section('header', 'Users')

@section('content')

    <div class="admin-page-header">
        <h1 class="admin-page-title">
            Users
        </h1>

        <p class="admin-page-description">
            Manage users and review their account information.
        </p>
    </div>

    <div class="admin-card">

        <form
            method="GET"
            action="{{ route('larakit.admin.users.index') }}"
            style="
                display: flex;
                gap: 10px;
                margin-bottom: 22px;
            "
        >
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search users..."
                style="
                    flex: 1;
                    min-width: 0;
                    padding: 10px 12px;
                    border: 1px solid #d1d5db;
                    border-radius: 7px;
                    font: inherit;
                "
            >

            <button
                type="submit"
                style="
                    padding: 10px 18px;
                    border: 0;
                    border-radius: 7px;
                    background: #111827;
                    color: #ffffff;
                    cursor: pointer;
                    font: inherit;
                "
            >
                Search
            </button>
        </form>

        @if ($users->count() > 0)

            <div style="overflow-x: auto;">
                <table
                    style="
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 14px;
                    "
                >
                    <thead>
                        <tr>
                            <th
                                style="
                                    text-align: left;
                                    padding: 12px;
                                    border-bottom: 1px solid #e5e7eb;
                                    color: #6b7280;
                                    font-weight: 600;
                                "
                            >
                                User
                            </th>

                            <th
                                style="
                                    text-align: left;
                                    padding: 12px;
                                    border-bottom: 1px solid #e5e7eb;
                                    color: #6b7280;
                                    font-weight: 600;
                                "
                            >
                                Email
                            </th>

                            <th
                                style="
                                    text-align: left;
                                    padding: 12px;
                                    border-bottom: 1px solid #e5e7eb;
                                    color: #6b7280;
                                    font-weight: 600;
                                "
                            >
                                Account
                            </th>

                            <th
                                style="
                                    text-align: left;
                                    padding: 12px;
                                    border-bottom: 1px solid #e5e7eb;
                                    color: #6b7280;
                                    font-weight: 600;
                                "
                            >
                                Roles
                            </th>

                            <th
                                style="
                                    text-align: right;
                                    padding: 12px;
                                    border-bottom: 1px solid #e5e7eb;
                                    color: #6b7280;
                                    font-weight: 600;
                                "
                            >
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($users as $user)

                            <tr>
                                <td
                                    style="
                                        padding: 14px 12px;
                                        border-bottom: 1px solid #f0f1f3;
                                    "
                                >
                                    <strong>
                                        {{ $user->displayName }}
                                    </strong>
                                </td>

                                <td
                                    style="
                                        padding: 14px 12px;
                                        border-bottom: 1px solid #f0f1f3;
                                        color: #6b7280;
                                    "
                                >
                                    {{ $user->email ?? '—' }}
                                </td>

                                <td
                                    style="
                                        padding: 14px 12px;
                                        border-bottom: 1px solid #f0f1f3;
                                    "
                                >
                                    @if ($user->accountStatus === true)
                                        <span
                                            style="
                                                display: inline-block;
                                                padding: 4px 8px;
                                                border-radius: 999px;
                                                font-size: 12px;
                                                background: #ecfdf5;
                                                color: #047857;
                                            "
                                        >
                                            Active
                                        </span>
                                    @elseif ($user->accountStatus === false)
                                        <span
                                            style="
                                                display: inline-block;
                                                padding: 4px 8px;
                                                border-radius: 999px;
                                                font-size: 12px;
                                                background: #fef2f2;
                                                color: #b91c1c;
                                            "
                                        >
                                            Inactive
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>

                                <td
                                    style="
                                        padding: 14px 12px;
                                        border-bottom: 1px solid #f0f1f3;
                                    "
                                >
                                    @if (count($user->roles) > 0)
                                        {{ implode(', ', $user->roles) }}
                                    @else
                                        —
                                    @endif
                                </td>

                                <td
                                    style="
                                        padding: 14px 12px;
                                        border-bottom: 1px solid #f0f1f3;
                                        text-align: right;
                                    "
                                >
                                    <a
                                        href="{{ route(
                                            'larakit.admin.users.show',
                                            ['id' => $user->id]
                                        ) }}"
                                        style="
                                            color: #2563eb;
                                            text-decoration: none;
                                            font-weight: 500;
                                        "
                                    >
                                        View
                                    </a>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div
                    style="
                        margin-top: 22px;
                    "
                >
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif

        @else

            <div
                style="
                    padding: 40px 20px;
                    text-align: center;
                    color: #6b7280;
                "
            >
                <strong
                    style="
                        display: block;
                        margin-bottom: 6px;
                        color: #374151;
                    "
                >
                    No users found.
                </strong>

                <span>
                    Try changing your search criteria.
                </span>
            </div>

        @endif

    </div>

@endsection