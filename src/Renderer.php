<?php

namespace DoekeNorg\Decreator;

use DoekeNorg\Decreator\Reader\ClassReader;
use DoekeNorg\Decreator\Reader\Method;

final class Renderer
{
    private array $class_names = [];

    public function __construct(private readonly ClassReader $reader)
    {
    }

    public function output(Request $request): string
    {
        $this->class_names = [];

        $methods = $this->reader->getMethods($source = $request->source());
        $namespaces = $this->renderNamespace($destination = $request->destination());

        $output = trim(
            sprintf(
                '%s class %s %s %s {',
                $this->getClassType($request),
                $this->getBaseClassName($destination),
                $this->reader->isInterface($source) ? 'implements' : 'extends',
                $this->sanitizeClassName($source),
            )
        );

        $output .= $this->renderInnerReference($request);
        $output .= $this->renderConstructor($request);

        foreach ($methods as $method) {
            $output .= $this->renderMethod($request, $method);
        }

        if ($spaces = $request->spaces()) {
            $output = str_replace("\t", str_repeat(' ', $spaces), $output);
        }

        $output .= '}';

        $output = strtr($output, $this->class_names);
        $uses = $this->renderUse($destination);

        return $namespaces . $uses . $output;
    }

    private function renderMethod(Request $request, Method $method): string
    {
        $this->recordClasses($method->returnType());
        foreach ($method as $argument) {
            $this->recordClasses((string) $argument->type());
        }

        $output = PHP_EOL . "\t" . $method . ' {' . PHP_EOL;
        if (!$method->isStatic()) {
            $output .= "\t\t" . sprintf(
                    '%s$this->%s->%s(%s);',
                    $method->isVoid() ? '' : 'return ',
                    $request->variable(),
                    $method->name(),
                    $method->hasArguments() ? '...func_get_args()' : '',
                ) . PHP_EOL;
        }
        $output .= "\t}" . PHP_EOL;

        return $output;
    }

    private function renderInnerReference(Request $request): string
    {
        return PHP_EOL . "\t" . sprintf(
                'private %s $%s;',
                $this->sanitizeClassName($request->source()),
                $request->variable(),
            ) . PHP_EOL . PHP_EOL;
    }

    //Todo: constructor can be part of the interface.
    private function renderConstructor(Request $request): string
    {
        $output = sprintf(
                "\tpublic function __construct(%s $%s) {",
                $this->sanitizeClassName($request->source()),
                $request->variable(),
            ) . PHP_EOL;

        $output .= "\t\t" . sprintf('$this->%s = $%s;', $request->variable(), $request->variable()) . PHP_EOL;
        $output .= "\t}" . PHP_EOL;

        return $output;
    }

    private function getNamespace($class_name): string
    {
        $base_class_name = $this->getBaseClassName($class_name);
        if ($class_name === $base_class_name) {
            return '';
        }

        return trim(rtrim($class_name, $base_class_name), '\\');
    }

    private function renderNamespace(string $class_name): string
    {
        if (!$namespace = $this->getNamespace($class_name)) {
            return '';
        }

        return PHP_EOL . 'namespace ' . $namespace . ';' . PHP_EOL . PHP_EOL;
    }

    private function getBaseClassName(string $class_name): string
    {
        $parts = explode('\\', $class_name);
        return end($parts);
    }

    private function recordClasses(string $class_name): void
    {
        $class_name = str_replace('?', '', $class_name);
        if (in_array(trim($class_name), ['', 'string', 'array', 'int', 'void', 'float'])) {
            return;
        }
        $class_name = trim($class_name, '\\');

        $base_name = $this->getBaseClassName($class_name);
        if (!in_array($base_name, $this->class_names, true)) {
            $this->class_names[$class_name] ??= $base_name;
        }
    }

    private function sanitizeClassName(string $class_name): string
    {
        $class_name = '\\' . trim($class_name, '\\');
        $this->recordClasses($class_name);

        return $class_name;
    }

    private function getClassType(Request $request): string
    {
        return match ($request->type()) {
            ClassType::Abstract => 'abstract',
            ClassType::Final => 'final',
            default => '',
        };
    }

    private function renderUse(string $destination): string
    {
        $class_names = array_filter(
            array_keys($this->class_names),
            fn(string $class_name) => $this->getNamespace($destination) !== $this->getNamespace($class_name)
        );

        if (!$class_names) {
            return '';
        }

        sort($class_names);

        $output = '';
        foreach ($class_names as $class_name) {
            $output .= sprintf('use %s;', trim($class_name, '\\')) . PHP_EOL;
        }

        return $output . PHP_EOL;
    }
}
