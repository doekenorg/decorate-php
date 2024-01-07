<?php

declare(strict_types=1);

namespace DoekeNorg\DecoratePhp\Reader;

final class Arguments implements \Stringable, \Countable
{
    /**
     * @var Argument[]
     */
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

    /**
     * @return Argument[]
     */
    public function getArguments():array
    {
        return $this->arguments;
    }
}
