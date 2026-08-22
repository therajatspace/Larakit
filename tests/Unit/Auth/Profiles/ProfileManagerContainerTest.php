<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Profiles;

use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\Profiles\ProfileManager;
use Therajatspace\Larakit\LaraKitServiceProvider;

class ProfileManagerContainerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
        ];
    }

    public function test_profile_manager_can_be_resolved(): void
    {
        config()->set('larakit.auth.profiles', [
            'internal' => [
                'role' => 'staff',
                'login' => true,
                'registration' => false,
            ],
        ]);

        $manager = $this->app->make(ProfileManager::class);

        $this->assertTrue(
            $manager->has('internal')
        );

        $this->assertSame(
            'staff',
            $manager->find('internal')->role()
        );
    }
}