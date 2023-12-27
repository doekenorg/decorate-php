<?php

namespace DoekeNorg\Decreator\Reader;

interface InterfaceReader
{
    /**
     * @return Method[]
     */
    public function getMethods(string $interface_name): array;
}