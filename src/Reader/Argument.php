<?php

namespace DoekeNorg\Decreator\Reader;

final class Argument implements \Stringable
{
    public function __construct(
        private readonly string $name,
        private readonly ?string $type = null,
        private readonly mixed $default_value = null,
        private readonly bool $is_variadic = false,
    ) {
    }

    public function __toString(): string
    {
        return trim(
            ($this->type ?: '') . ' '
            . ($this->is_variadic ? '...' : '')
            . '$' . $this->name
            . ($this->default_value ? ' = ' . $this->default_value : '')
        );
    }

    public function type(): string
    {
        return (string) $this->type;
    }
}
