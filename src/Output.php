<?php

namespace DoekeNorg\Decreator;

use DoekeNorg\Decreator\Reader\InterfaceReader;
use DoekeNorg\Decreator\Reader\Method;

/**
 * Todo:
 *  - output should be configurable as abstract or final.
 *  - The name of the inner variable should be configurable.
 */
final class Output
{
    private string $inner_variable = 'inner';

    public function __construct(private readonly InterfaceReader $reader)
    {
    }

    public function output(string $source, string $destination): string
    {
        $methods = $this->reader->getMethods($source);
        $output = $this->renderNamespace($destination);

        $output .= sprintf(
            'class %s %s %s {',
            $this->getBaseClassName($destination),
            $this->reader->isAbstract($source) ? 'extends' : 'implements',
            $this->sanitizeInterfaceName($source),
        );

        $output .= $this->renderInnerReference($source);
        $output .= $this->renderConstructor($source);

        foreach ($methods as $method) {
            $output .= $this->renderMethod($method);
        }

        $output .= '}';

        return $output;
    }

    private function renderMethod(Method $method): string
    {
        $output = PHP_EOL . "\t" . $method . ' {' . PHP_EOL;
        if (!$method->isStatic()) {
            $output .= "\t\t" . sprintf(
                    '%s$this->%s->%s(%s);',
                    $method->isVoid() ? '' : 'return ',
                    $this->inner_variable,
                    $method->name(),
                    $method->hasArguments() ? '...func_get_args()' : '',
                ) . PHP_EOL;
        }
        $output .= "\t}" . PHP_EOL;

        return $output;
    }

    private function renderInnerReference(string $interface_name): string
    {
        return PHP_EOL . "\t" . sprintf(
                'private %s $%s;',
                $this->sanitizeInterfaceName($interface_name),
                $this->inner_variable
            ) . PHP_EOL . PHP_EOL;
    }

    //Todo: constructor can be part of the interface.
    private function renderConstructor(string $interface_name): string
    {
        $output = sprintf(
                "\tpublic function __construct(%s $%s) {",
                $this->sanitizeInterfaceName($interface_name),
                $this->inner_variable,
            ) . PHP_EOL;

        $output .= "\t\t" . sprintf('$this->%s = $%s;', $this->inner_variable, $this->inner_variable) . PHP_EOL;
        $output .= "\t}" . PHP_EOL;

        return $output;
    }

    private function renderNamespace(string $class_name): string
    {
        $base_class_name = $this->getBaseClassName($class_name);
        if ($class_name === $base_class_name) {
            return '';
        }

        // todo: this is too naive, only replace the last bit.
        return PHP_EOL . 'namespace ' . trim(
                str_replace('\\' . $base_class_name, '', $class_name),
                '\\'
            ) . ';' . PHP_EOL . PHP_EOL;
    }

    private function getBaseClassName(string $class_name): string
    {
        $parts = explode('\\', $class_name);
        return end($parts);
    }

    private function sanitizeInterfaceName(string $interface_name): string
    {
        return '\\' . trim($interface_name, '\\');
    }
}