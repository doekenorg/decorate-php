<?php

namespace DoekeNorg\Decreator\Reader;

/**
 * Represents a method on the interface.
 * @since $ver$
 */
final class Method implements \Stringable
{
    public function __construct(
        private readonly string $name,
        private readonly Arguments $arguments,
        private readonly Visibility $visibility = Visibility::Public,
        private readonly ?string $return_type = null,
        private readonly bool $is_static = false,
        private readonly bool $is_final = false,
    ) {
    }

    public function __toString(): string
    {
        return
            ($this->is_final ? 'final ' : '')
            . $this->visibility->value
            . ($this->is_static ? ' static' : '')
            . ' function '
            . $this->name
            . $this->arguments
            . $this->returnType();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function returnType(): string
    {
        return $this->return_type ? ': ' . $this->return_type : '';
    }

    public function isVoid(): bool
    {
        return $this->return_type === 'void';
    }

    public function isStatic(): bool
    {
        return $this->is_static;
    }
}