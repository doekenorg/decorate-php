<?php

namespace DoekeNorg\Decreator\Composer;

use Composer\Plugin\Capability\CommandProvider;

final class DecreateCommandProvider implements CommandProvider
{
    public function getCommands(): array
    {
        return [
            new DecorateCommand(),
        ];
    }
}