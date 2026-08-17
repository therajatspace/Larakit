<?php

namespace Therajatspace\Larakit\Tests\Unit\Install;

use PHPUnit\Framework\TestCase;
use Therajatspace\Larakit\Install\SeoInstaller;

class SeoInstallerTest extends TestCase
{
    public function test_seo_installer_reports_that_seo_is_already_available(): void
    {
        $installer = new SeoInstaller();

        $result = $installer->install();

        $this->assertSame(
            'SEO module is already available and requires no additional setup.',
            $result
        );
    }
}