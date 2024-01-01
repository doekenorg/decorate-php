<?php

namespace DoekeNorg\Decreator\Writer;

interface PathResolver
{
    /**
     * @throws PathNotFound
     */
    public function resolveFromClass(string $class_name): ?string;
}
