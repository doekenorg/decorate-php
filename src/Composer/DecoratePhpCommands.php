<?php

declare(strict_types=1);

namespace DoekeNorg\DecoratePhp\Composer;

use Composer\Plugin\Capability\CommandProvider;

final class DecoratePhpCommands implements CommandProvider
{
    public function getCommands(): array
    {
        return [
            new DecorateCommand(),
        ];
    }
}
