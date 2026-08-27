<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Admin')
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            background: #f5f7fa;
            color: #1f2937;
        }

        body {
            min-height: 100vh;
        }

        .admin-shell {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 240px;
            flex-shrink: 0;
            background: #111827;
            color: #ffffff;
            padding: 24px 16px;
        }

        .admin-brand {
            padding: 0 12px 28px;
        }

        .admin-brand-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .admin-brand-subtitle {
            margin: 5px 0 0;
            font-size: 12px;
            color: #9ca3af;
        }

        .admin-navigation {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .admin-navigation a {
            display: block;
            padding: 10px 12px;
            border-radius: 7px;
            color: #d1d5db;
            text-decoration: none;
            font-size: 14px;
        }

        .admin-navigation a:hover {
            background: #1f2937;
            color: #ffffff;
        }

        .admin-navigation a.active {
            background: #374151;
            color: #ffffff;
        }

        .admin-main {
            flex: 1;
            min-width: 0;
        }

        .admin-header {
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        .admin-header-title {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .admin-header-user {
            font-size: 14px;
            color: #6b7280;
        }

        .admin-content {
            padding: 28px;
        }

        .admin-page-header {
            margin-bottom: 24px;
        }

        .admin-page-title {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .admin-page-description {
            margin: 7px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .admin-cards {
            display: grid;
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .admin-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 22px;
        }

        .admin-card-label {
            margin: 0;
            font-size: 13px;
            color: #6b7280;
        }

        .admin-card-value {
            margin: 8px 0 0;
            font-size: 28px;
            font-weight: 700;
        }

        .admin-card-description {
            margin: 7px 0 0;
            font-size: 12px;
            color: #9ca3af;
        }

        @media (max-width: 800px) {
            .admin-sidebar {
                width: 190px;
            }

            .admin-cards {
                grid-template-columns:
                    1fr;
            }
        }

        @media (max-width: 600px) {
            .admin-shell {
                display: block;
            }

            .admin-sidebar {
                width: 100%;
            }

            .admin-navigation {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .admin-main {
                width: 100%;
            }

            .admin-header {
                padding: 0 18px;
            }

            .admin-content {
                padding: 20px 18px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="admin-shell">

        <aside class="admin-sidebar">
            <div class="admin-brand">
                <h1 class="admin-brand-title">
                    LaraKit
                </h1>

                <p class="admin-brand-subtitle">
                    Administration
                </p>
            </div>

            <nav class="admin-navigation">
                <a href="{{ route('larakit.admin.dashboard') }}" class="active">
                    Dashboard
                </a>

                @if (config('larakit.admin.modules.users', false))
                    <a href="{{ route('larakit.admin.users.index') }}">
                        Users
                    </a>
                @endif

                @if (config('larakit.admin.modules.authorization', false))
                    <a href="#">
                        Authorization
                    </a>
                @endif

                @if (config('larakit.admin.modules.website_health', false))
                    <a href="#">
                        Website Health
                    </a>
                @endif

                @if (config('larakit.admin.modules.seo_health', false))
                    <a href="#">
                        SEO Health
                    </a>
                @endif

                @if (config('larakit.admin.modules.traffic', false))
                    <a href="#">
                        Traffic
                    </a>
                @endif

                @if (config('larakit.admin.modules.audit', false))
                    <a href="#">
                        Audit
                    </a>
                @endif
            </nav>
        </aside>

        <main class="admin-main">

            <header class="admin-header">
                <h2 class="admin-header-title">
                    @yield('header', 'Admin')
                </h2>

                <div class="admin-header-user">
                    Administration
                </div>
            </header>

            <section class="admin-content">
                @yield('content')
            </section>

        </main>

    </div>

    @stack('scripts')
</body>

</html>