@extends('larakit::admin.layouts.app')

@section('title', 'User Details')

@section('header', 'User Details')

@section('content')

    <div class="admin-page-header">
        <h1 class="admin-page-title">
            {{ $user->displayName }}
        </h1>

        <p class="admin-page-description">
            User account details and authorization information.
        </p>
    </div>

    <div
        style="
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        "
    >

        <div class="admin-card">

            <h2
                style="
                    margin: 0 0 20px;
                    font-size: 17px;
                "
            >
                Account
            </h2>

            <div style="margin-bottom: 16px;">
                <div
                    style="
                        font-size: 12px;
                        color: #6b7280;
                        margin-bottom: 4px;
                    "
                >
                    Name
                </div>

                <div>
                    {{ $user->displayName }}
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <div
                    style="
                        font-size: 12px;
                        color: #6b7280;
                        margin-bottom: 4px;
                    "
                >
                    Email
                </div>

                <div>
                    {{ $user->email ?? '—' }}
                </div>
            </div>

            <div>
                <div
                    style="
                        font-size: 12px;
                        color: #6b7280;
                        margin-bottom: 4px;
                    "
                >
                    Account Status
                </div>

                <div>
                    @if ($user->accountStatus === true)
                        Active
                    @elseif ($user->accountStatus === false)
                        Inactive
                    @else
                        —
                    @endif
                </div>
            </div>

        </div>

        <div class="admin-card">

            <h2
                style="
                    margin: 0 0 20px;
                    font-size: 17px;
                "
            >
                Roles
            </h2>

            @if (count($user->roles) > 0)

                <ul
                    style="
                        margin: 0;
                        padding-left: 20px;
                    "
                >
                    @foreach ($user->roles as $role)
                        <li style="margin-bottom: 7px;">
                            {{ $role }}
                        </li>
                    @endforeach
                </ul>

            @else

                <p
                    style="
                        margin: 0;
                        color: #6b7280;
                    "
                >
                    No roles assigned.
                </p>

            @endif

        </div>

        <div class="admin-card">

            <h2
                style="
                    margin: 0 0 20px;
                    font-size: 17px;
                "
            >
                Permissions
            </h2>

            @if (count($user->permissions) > 0)

                <ul
                    style="
                        margin: 0;
                        padding-left: 20px;
                    "
                >
                    @foreach ($user->permissions as $permission)
                        <li style="margin-bottom: 7px;">
                            {{ $permission }}
                        </li>
                    @endforeach
                </ul>

            @else

                <p
                    style="
                        margin: 0;
                        color: #6b7280;
                    "
                >
                    No permissions assigned.
                </p>

            @endif

        </div>

    </div>

    <div style="margin-top: 24px;">
        <a
            href="{{ route('larakit.admin.users.index') }}"
            style="
                color: #2563eb;
                text-decoration: none;
                font-size: 14px;
                font-weight: 500;
            "
        >
            ← Back to users
        </a>
    </div>

@endsection