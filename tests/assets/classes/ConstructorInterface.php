<?php

namespace DoekeNorg\DecoratePhp\Tests\assets\classes;

namespace Acme;

interface ConstructorInterface
{
    public function __construct(string $param);

    public function method(int ...$vars): string;
}
