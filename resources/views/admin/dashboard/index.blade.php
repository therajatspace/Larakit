@extends('larakit::admin.layouts.app')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')

    <div class="admin-page-header">
        <h1 class="admin-page-title">
            Dashboard
        </h1>

        <p class="admin-page-description">
            Welcome to the LaraKit administration panel.
        </p>
    </div>

    <div class="admin-cards">

        @if (config('larakit.admin.modules.users', false))
            <div class="admin-card">
                <p class="admin-card-label">
                    Users
                </p>

                <p class="admin-card-value">
                    —
                </p>

                <p class="admin-card-description">
                    User management will appear here.
                </p>
            </div>
        @endif

        @if (config('larakit.admin.modules.authorization', false))
            <div class="admin-card">
                <p class="admin-card-label">
                    Authorization
                </p>

                <p class="admin-card-value">
                    —
                </p>

                <p class="admin-card-description">
                    Roles and permissions will appear here.
                </p>
            </div>
        @endif

        @if (config('larakit.admin.modules.website_health', false))
            <div class="admin-card">
                <p class="admin-card-label">
                    Website Health
                </p>

                <p class="admin-card-value">
                    —
                </p>

                <p class="admin-card-description">
                    Website health information will appear here.
                </p>
            </div>
        @endif

    </div>

@endsection