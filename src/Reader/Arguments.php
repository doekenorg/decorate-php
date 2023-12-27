<?php

namespace DoekeNorg\Decreator\Reader;

final class Arguments implements \Stringable
{
    private array $arguments;

    public function __construct(Argument ...$arguments)
    {
        $this->arguments = $arguments;
    }

    public function __toString(): string
    {
        return '(' . implode(
                ', ',
                array_map(fn(Argument $argument): string => (string) $argument, $this->arguments)
            ) . ')';
    }
}