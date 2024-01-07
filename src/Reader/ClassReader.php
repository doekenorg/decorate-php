<?php

declare(strict_types=1);

namespace DoekeNorg\DecoratePhp\Reader;

interface ClassReader
{
    /**
     * @return Method[]
     */
    public function getMethods(string $class_name): array;

    public function isInterface(string $class_name): bool;

    public function isFinal(string $class_name): bool;
}
