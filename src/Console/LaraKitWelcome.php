<?php

namespace Sidd2604\Larakit\Console;

class LaraKitWelcome
{
    public static function show(): void
    {
        $cyan    = "\033[36m";
        $green   = "\033[32m";
        $yellow  = "\033[33m";
        $magenta = "\033[35m";
        $white   = "\033[37m";
        $gray    = "\033[90m";
        $bold    = "\033[1m";
        $reset   = "\033[0m";

        echo PHP_EOL;

        echo $cyan . $bold . <<<ASCII

    ██╗      █████╗ ██████╗  █████╗ ██╗  ██╗██╗████████╗
    ██║     ██╔══██╗██╔══██╗██╔══██╗██║ ██╔╝██║╚══██╔══╝
    ██║     ███████║██████╔╝███████║█████╔╝ ██║   ██║
    ██║     ██╔══██║██╔══██╗██╔══██║██╔═██╗ ██║   ██║
    ███████╗██║  ██║██║  ██║██║  ██║██║  ██╗██║   ██║
    ╚══════╝╚═╝  ╚═╝╚═╝  ╚═╝╚═╝  ╚═╝╚═╝  ╚═╝╚═╝   ╚═╝

ASCII;

        echo $reset . PHP_EOL;

        echo $green . $bold;
        echo "    🚀 LaraKit Installed Successfully!";
        echo $reset . PHP_EOL;

        echo $gray;
        echo "    Your Laravel super toolkit is ready to use.";
        echo $reset . PHP_EOL . PHP_EOL;

        echo $cyan . $bold;
        echo "    ┌──────────────────────────────────────────────┐";
        echo $reset . PHP_EOL;

        echo "    │  {$green}✓{$reset}  {$white}{$bold}SEO Optimization{$reset}              {$gray}Grow your visibility{$reset}  │" . PHP_EOL;
        echo "    │  {$green}✓{$reset}  {$white}{$bold}Image Optimization{$reset}            {$gray}Faster websites{$reset}    │" . PHP_EOL;
        echo "    │  {$green}✓{$reset}  {$white}{$bold}Authentication{$reset}                 {$gray}Secure your users{$reset}  │" . PHP_EOL;
        echo "    │  {$green}✓{$reset}  {$white}{$bold}Admin Panel{$reset}                    {$gray}Manage with ease{$reset}  │" . PHP_EOL;

        echo $cyan . $bold;
        echo "    └──────────────────────────────────────────────┘";
        echo $reset . PHP_EOL . PHP_EOL;

        echo $yellow . $bold;
        echo '    ✨ "Build smarter. Optimize better. Ship faster."';
        echo $reset . PHP_EOL;

        echo $magenta;
        echo "    Made for developers who want more from Laravel.";
        echo $reset . PHP_EOL . PHP_EOL;

        echo $green . $bold;
        echo "    ❤️  Happy Building with LaraKit!  ~   Siddharth Sharma";
        echo $reset . PHP_EOL . PHP_EOL;
    }
}
