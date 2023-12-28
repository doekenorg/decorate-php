<?php

namespace DoekeNorg\Decreator\Reader;

interface InterfaceReader
{
    /**
     * @return Method[]
     */
    public function getMethods(string $class_name): array;

    public function isInterface(string $class_name): bool;

    public function isAbstract(string $class_name): bool;
}