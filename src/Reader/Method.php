<?php

namespace DoekeNorg\DecoratePhp\Reader;

/**
 * Represents a method on the interface.
 * @since $ver$
 */
final class Method implements \Stringable, \IteratorAggregate
{
    public function __construct(
        private readonly string $name,
        private readonly Arguments $arguments,
        private readonly Visibility $visibility = Visibility::Public,
        private readonly ?string $return_type = null,
        private readonly bool $is_static = false,
        private readonly bool $is_final = false,
        private readonly bool $has_parent = false,
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
            . ($this->return_type ? ': ' . $this->return_type : '');
    }

    public function name(): string
    {
        return $this->name;
    }

    public function returnType(): string
    {
        return $this->return_type ?? '';
    }

    public function isVoid(): bool
    {
        return $this->return_type === 'void' || $this->isConstructor();
    }

    public function isStatic(): bool
    {
        return $this->is_static;
    }

    public function isConstructor(): bool
    {
        return $this->name === '__construct';
    }

    public function hasArguments(): bool
    {
        return count($this->arguments);
    }

    public function hasParent(): bool
    {
        return $this->has_parent;
    }

    public function getIterator(): \Traversable
    {
        return $this->arguments;
    }
}
