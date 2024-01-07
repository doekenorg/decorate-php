<?php

declare(strict_types=1);

namespace DoekeNorg\DecoratePhp\Renderer;

final class RenderRequest
{
    private ClassType $class_type;

    private string $variable = 'next';

    private int $spaces = 0;

    private bool $use_property_promotion = true;

    private bool $use_func_get_args = false;

    private bool $declare_strict = false;

    public function __construct(
        private readonly string $source,
        private readonly string $destination,
    ) {
        $this->class_type = ClassType::Normal;
    }

    public static function asAbstract(string $source, string $destination): self
    {
        $request = new self($source, $destination);
        $request->class_type = ClassType::Abstract;

        return $request;
    }

    public static function asFinal(string $source, string $destination): self
    {
        $request = new self($source, $destination);
        $request->class_type = ClassType::Final;

        return $request;
    }

    public function variable(): string
    {
        return $this->variable;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function destination(): string
    {
        return $this->destination;
    }

    public function spaces(): int
    {
        return $this->spaces;
    }

    public function type(): ClassType
    {
        return $this->class_type;
    }

    public function usePropertyPromotion(): bool
    {
        return $this->use_property_promotion;
    }

    public function useFuncGetArgs(): bool
    {
        return $this->use_func_get_args;
    }

    public function declareStrict(): bool
    {
        return $this->declare_strict;
    }

    public function withVariable(string $variable): self
    {
        $clone = clone $this;
        $clone->variable = $variable;

        return $clone;
    }

    public function withSpaces(int $spaces = 4): self
    {
        $clone = clone $this;
        if ($spaces < 1) {
            $spaces = 0;
        }

        $clone->spaces = $spaces;

        return $clone;
    }

    public function withoutPropertyPromotion(): self
    {
        $clone = clone $this;
        $clone->use_property_promotion = false;

        return $clone;
    }

    public function withFuncGetArgs(): self
    {
        $clone = clone $this;
        $clone->use_func_get_args = true;

        return $clone;
    }

    public function withStrict(): self
    {
        $clone = clone $this;
        $clone->declare_strict = true;

        return $clone;
    }
}
