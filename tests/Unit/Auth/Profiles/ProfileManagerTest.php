<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Profiles;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\Auth\Profiles\AuthProfile;
use Therajatspace\Larakit\Auth\Profiles\ProfileManager;

class ProfileManagerTest extends TestCase
{
    public function test_profiles_are_registered(): void
    {
        $manager = new ProfileManager([
            'internal' => [
                'role' => 'staff',
            ],

            'public' => [
                'role' => 'member',
            ],
        ]);

        $this->assertTrue($manager->has('internal'));
        $this->assertTrue($manager->has('public'));
    }

    public function test_find_returns_profile(): void
    {
        $manager = new ProfileManager([
            'internal' => [
                'role' => 'staff',
            ],
        ]);

        $profile = $manager->find('internal');

        $this->assertInstanceOf(
            AuthProfile::class,
            $profile
        );

        $this->assertSame(
            'internal',
            $profile->name()
        );
    }

    public function test_find_returns_null_for_unknown_profile(): void
    {
        $manager = new ProfileManager();

        $this->assertNull(
            $manager->find('unknown')
        );
    }

    public function test_all_returns_all_profiles(): void
    {
        $manager = new ProfileManager([
            'one' => [],
            'two' => [],
        ]);

        $profiles = $manager->all();

        $this->assertCount(2, $profiles);
        $this->assertArrayHasKey('one', $profiles);
        $this->assertArrayHasKey('two', $profiles);
    }

    public function test_duplicate_profile_is_rejected(): void
    {
        $manager = new ProfileManager();

        $manager->add('internal');

        $this->expectException(InvalidArgumentException::class);

        $manager->add('internal');
    }
}