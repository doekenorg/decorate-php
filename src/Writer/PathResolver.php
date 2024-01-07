<?php

declare(strict_types=1);

namespace DoekeNorg\DecoratePhp\Writer;

interface PathResolver
{
    /**
     * @throws PathNotFound
     */
    public function resolveFromClass(string $class_name): ?string;
}
