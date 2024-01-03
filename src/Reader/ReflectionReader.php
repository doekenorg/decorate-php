<?php

namespace DoekeNorg\DecoratePhp\Reader;

use ReflectionException;

final class ReflectionReader implements ClassReader
{
    private const TYPE_VALUE_REGEX = '/Parameter #\d+ \[ \<\w+> (?<type>\S+) \S+? (= (?<value>\S+) )?\]/is';

    public function getMethods(string $class_name): array
    {
        try {
            $class = new \ReflectionClass($class_name);
        } catch (ReflectionException) {
            throw new ClassNotFound();
        }

        $methods = array_filter($class->getMethods(), $this->removeFinalMethods(...));

        return array_values(array_map($this->createMethodFromReflection(...), $methods));
    }

    private function removeFinalMethods(\ReflectionMethod $method): bool
    {
        return !$method->isFinal();
    }

    private function createMethodFromReflection(\ReflectionMethod $method): Method
    {
        $method_name = $method->getName();

        return new Method(
            $method_name,
            $this->getArguments($method),
            $this->getVisibility($method),
            $this->getReturnType($method),
            $method->isStatic(),
            $method->isFinal(),
        );
    }

    private function getVisibility(\ReflectionMethod $method): Visibility
    {
        return match (true) {
            $method->isPrivate() => Visibility::Private,
            $method->isProtected() => Visibility::Protected,
            default => Visibility::Public,
        };
    }

    private function getArguments(\ReflectionMethod $method): Arguments
    {
        $arguments = array_map(
            $this->getArgument(...),
            $method->getParameters(),
        );

        return new Arguments(...$arguments);
    }

    private function getArgument(\ReflectionParameter $parameter): Argument
    {
        return new Argument(
            $parameter->getName(),
            $this->getArgumentType($parameter),
            $this->getDefaultValue($parameter),
            $parameter->isVariadic(),
        );
    }

    private function getReturnType(\ReflectionMethod $method): ?string
    {
        if (!$return_type = $method->getReturnType()) {
            return null;
        }

        $output = '';
        if (!$return_type->isBuiltin()) {
            $output .= '\\';
        }

        return $output . trim($return_type->getName(), '\\');
    }

    private function getArgumentType(\ReflectionParameter $parameter): ?string
    {
        $matches = [];

        if (!preg_match(
            self::TYPE_VALUE_REGEX,
            (string) $parameter,
            $matches,
        )) {
            return null;
        }

        return $matches['type'] ?? null;
    }

    private function getDefaultValue(\ReflectionParameter $parameter): ?string
    {
        $matches = [];
        if (!preg_match(
            self::TYPE_VALUE_REGEX,
            (string) $parameter,
            $matches,
        )) {
            return null;
        }

        return $matches['value'] ?? null;
    }

    public function isInterface(string $class_name): bool
    {
        return interface_exists($class_name);
    }

    public function isFinal(string $class_name): bool
    {
        try {
            return (new \ReflectionClass($class_name))->isFinal();
        } catch (\ReflectionException) {
            return false;
        }
    }
}
