<?php

declare(strict_types=1);

namespace DoekeNorg\DecoratePhp\Renderer;

use DoekeNorg\DecoratePhp\Reader\Argument;
use DoekeNorg\DecoratePhp\Reader\Arguments;
use DoekeNorg\DecoratePhp\Reader\ClassReader;
use DoekeNorg\DecoratePhp\Reader\Method;
use DoekeNorg\DecoratePhp\Reader\Visibility;

final class PhpClassRenderer implements Renderer
{
    private array $class_names = [];

    public function __construct(private readonly ClassReader $reader)
    {
    }

    public function render(RenderRequest $request): string
    {
        if ($this->reader->isFinal($request->source())) {
            throw new CouldNotRender('Final classes cannot be decorated.');
        }

        $this->class_names = [];

        $methods = $this->getMethods($request);
        $namespaces = $this->renderNamespace($destination = $request->destination());

        $output = trim(
            sprintf(
                '%s class %s %s %s {',
                $this->getClassType($request),
                $this->getBaseClassName($destination),
                $this->reader->isInterface($source = $request->source()) ? 'implements' : 'extends',
                $this->sanitizeClassName($source),
            )
        );

        if (!$request->usePropertyPromotion()) {
            $output .= $this->renderInnerReference($request);
        }

        foreach ($methods as $method) {
            $output .= $this->renderMethod($request, $method);
        }

        if ($spaces = $request->spaces()) {
            $output = str_replace("\t", str_repeat(' ', $spaces), $output);
        }

        $output .= '}' . PHP_EOL;

        $replacements = [];

        if (!empty($this->class_names)) {
            foreach ($this->class_names as $class_name => $replacement) {
                $replacements[$class_name] = $replacement;
                $replacements['\\' . $class_name] = $replacement;
            }
        }

        $output = strtr($output, $replacements);
        $uses = $this->renderUse($destination);
        $strict = $request->declareStrict() ? 'declare(strict_types=1);' . PHP_EOL . PHP_EOL : '';

        return '<?php' . PHP_EOL . PHP_EOL . $strict . $namespaces . $uses . $output;
    }

    private function renderMethod(RenderRequest $request, Method $method): string
    {
        $this->recordClasses($method->returnType());
        foreach ($method as $argument) {
            $this->recordClasses((string) $argument->type());
        }

        $output = PHP_EOL . "\t" . $method . ' {' . PHP_EOL;
        if ($method->isConstructor()) {
            if ($method->hasParent()) {
                $output .= sprintf(
                    "\t\tparent::%s(%s);" . PHP_EOL,
                    $method->name(),
                    $this->getArguments($request, $method)
                );
            }

            if (!$request->usePropertyPromotion()) {
                $output .= "\t\t" . sprintf('$this->%s = $%s;', $request->variable(), $request->variable()) . PHP_EOL;
            }
        } elseif (!$method->isStatic()) {
            $output .= "\t\t" . sprintf(
                    '%s$this->%s->%s(%s);',
                    $method->isVoid() ? '' : 'return ',
                    $request->variable(),
                    $method->name(),
                    $this->getArguments($request, $method),
                ) . PHP_EOL;
        }
        $output .= "\t}" . PHP_EOL;

        return $output;
    }

    private function renderInnerReference(RenderRequest $request): string
    {
        return PHP_EOL . "\t" . sprintf(
                '%s %s $%s;',
                $request->type() === ClassType::Abstract ? 'protected' : 'private',
                $this->sanitizeClassName($request->source()),
                $request->variable(),
            ) . PHP_EOL;
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

        return 'namespace ' . $namespace . ';' . PHP_EOL . PHP_EOL;
    }

    private function getBaseClassName(string $class_name): string
    {
        $parts = explode('\\', $class_name);
        return end($parts);
    }

    private function recordClasses(string $class_name): void
    {
        $class_name = str_replace('?', '', $class_name);

        if (
            !class_exists($class_name)
            && !interface_exists($class_name)
            && !trait_exists($class_name)
        ) {
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

    private function getClassType(RenderRequest $request): string
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

    private function getArguments(RenderRequest $request, Method $method): string
    {
        if (!$method->hasArguments()) {
            return '';
        }

        if ($request->useFuncGetArgs()) {
            return '...func_get_args()';
        }
        $methods = iterator_to_array($method);

        if ($method->isConstructor()) {
            array_shift($methods);
        }

        return implode(
            ', ',
            array_map(
                static fn(Argument $argument): string => $argument->variable(),
                $methods,
            ),
        );
    }

    private function getMethods(RenderRequest $request): array
    {
        $constructor = null;

        $methods = array_filter(
            $this->reader->getMethods($request->source()),
            static function (Method $method) use (&$constructor): bool {
                if ($method->isConstructor()) {
                    $constructor = $method;
                    return false;
                }

                return true;
            },
        );

        $type = null;
        if ($request->usePropertyPromotion()) {
            $type = $request->type() === ClassType::Abstract ? Visibility::Protected : Visibility::Private;
        }

        $inner = new Argument(
            $request->variable(),
            $this->sanitizeClassName($request->source()),
            null,
            false,
            $type,
        );

        $arguments = $constructor !== null ? iterator_to_array($constructor) : [];
        $constructor = new Method(
            '__construct',
            new Arguments($inner, ...$arguments),
            Visibility::Public,
            null,
            false,
            false,
            $constructor !== null,
        );

        return array_merge([$constructor], $methods);
    }
}
