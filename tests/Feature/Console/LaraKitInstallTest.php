<?php

namespace Therajatspace\Larakit\Tests\Feature\Console;

use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\LaraKitServiceProvider;

class LaraKitInstallTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
        ];
    }

    public function test_install_command_is_registered(): void
    {
        $this->artisan('larakit:install', [
            '--seo' => true,
        ])
            ->assertExitCode(0);
    }

    public function test_seo_flag_installs_seo_module(): void
    {
        $this->artisan('larakit:install', [
            '--seo' => true,
        ])
            ->expectsOutput('LaraKit Installation')
            ->expectsOutput(
                '✓ SEO module is already available and requires no additional setup.'
            )
            ->expectsOutput('LaraKit installation completed.')
            ->assertExitCode(0);
    }

    public function test_auth_flag_reports_module_not_available(): void
    {
        $this->artisan('larakit:install', [
            '--auth' => true,
        ])
            ->expectsOutput('LaraKit Installation')
            ->expectsOutput(
                '⚠ Authentication module is not available yet.'
            )
            ->expectsOutput('LaraKit installation completed.')
            ->assertExitCode(0);
    }

    public function test_admin_flag_reports_module_not_available(): void
    {
        $this->artisan('larakit:install', [
            '--admin' => true,
        ])
            ->expectsOutput(
                '⚠ Admin Panel module is not available yet.'
            )
            ->assertExitCode(0);
    }

    public function test_image_flag_reports_module_not_available(): void
    {
        $this->artisan('larakit:install', [
            '--image' => true,
        ])
            ->expectsOutput(
                '⚠ Image Optimization module is not available yet.'
            )
            ->assertExitCode(0);
    }

    public function test_all_flag_selects_all_modules(): void
    {
        $this->artisan('larakit:install', [
            '--all' => true,
        ])
            ->expectsOutput(
                '✓ SEO module is already available and requires no additional setup.'
            )
            ->expectsOutput(
                '⚠ Authentication module is not available yet.'
            )
            ->expectsOutput(
                '⚠ Admin Panel module is not available yet.'
            )
            ->expectsOutput(
                '⚠ Image Optimization module is not available yet.'
            )
            ->expectsOutput('LaraKit installation completed.')
            ->assertExitCode(0);
    }

    public function test_multiple_module_flags_can_be_used_together(): void
    {
        $this->artisan('larakit:install', [
            '--seo' => true,
            '--auth' => true,
        ])
            ->expectsOutput(
                '✓ SEO module is already available and requires no additional setup.'
            )
            ->expectsOutput(
                '⚠ Authentication module is not available yet.'
            )
            ->assertExitCode(0);
    }
}