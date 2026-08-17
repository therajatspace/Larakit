<?php

namespace Therajatspace\Larakit\Console\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\multiselect;
use Therajatspace\Larakit\Install\SeoInstaller;

class LaraKitInstall extends Command
{
    protected $signature = 'larakit:install
                            {--seo : Install the SEO module}
                            {--auth : Install the Authentication module}
                            {--admin : Install the Admin Panel module}
                            {--image : Install the Image Optimization module}
                            {--all : Install all LaraKit modules}';

    protected $description = 'Install and configure LaraKit modules';

    public function handle(): int
    {
        $this->info('LaraKit Installation');
        $this->newLine();

        $modules = $this->getSelectedModules();

        if (empty($modules)) {
            $this->warn('No modules selected.');

            return self::SUCCESS;
        }

        $this->newLine();

        foreach ($modules as $module) {
            $this->installModule($module);
        }

        $this->newLine();
        $this->info('LaraKit installation completed.');

        return self::SUCCESS;
    }

    protected function getSelectedModules(): array
    {
        if ($this->option('all')) {
            return [
                'SEO',
                'Authentication',
                'Admin Panel',
                'Image Optimization',
            ];
        }

        $modules = [];

        if ($this->option('seo')) {
            $modules[] = 'SEO';
        }

        if ($this->option('auth')) {
            $modules[] = 'Authentication';
        }

        if ($this->option('admin')) {
            $modules[] = 'Admin Panel';
        }

        if ($this->option('image')) {
            $modules[] = 'Image Optimization';
        }

        if (!empty($modules)) {
            return $modules;
        }

        return $this->interactiveSelection();
    }

    protected function interactiveSelection(): array
    {
        return multiselect(
            label: 'Which modules do you want to install?',
            options: [
                'SEO',
                'Authentication',
                'Admin Panel',
                'Image Optimization',
            ],
            default: ['SEO'],
            required: false,
        );
    }

    protected function installModule(string $module): void
    {
        switch ($module) {
            case 'SEO':
                $installer = new SeoInstaller();

                $this->info('✓ ' . $installer->install());
                break;

            case 'Authentication':
                $this->warn('⚠ Authentication module is not available yet.');
                break;

            case 'Admin Panel':
                $this->warn('⚠ Admin Panel module is not available yet.');
                break;

            case 'Image Optimization':
                $this->warn('⚠ Image Optimization module is not available yet.');
                break;
        }
    }
}