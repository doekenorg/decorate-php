<?php

namespace DoekeNorg\DecoratePhp\Renderer;

final class RenderRequest
{
    private ClassType $class_type;

    private string $variable = 'inner';

    private int $spaces = 0;

    private bool $use_property_promotion = true;

    public function __construct(private string $source, private string $destination)
    {
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
}
