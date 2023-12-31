<?php

namespace DoekeNorg\Decreator\Reader;

use ArrayIterator;
use Traversable;

final class Arguments implements \Stringable, \Countable, \IteratorAggregate
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
                array_map(static fn(Argument $argument): string => (string) $argument, $this->arguments)
            ) . ')';
    }

    public function count(): int
    {
        return count($this->arguments);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->arguments);
    }
}
