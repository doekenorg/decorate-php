<?php

namespace DoekeNorg\DecoratePhp\Tests\Reader;

use Acme\AbstractClass;
use Acme\BasicInterface;
use Acme\ExtendingInterface;
use DoekeNorg\DecoratePhp\Reader\Method;
use DoekeNorg\DecoratePhp\Reader\ReflectionReader;
use PHPUnit\Framework\TestCase;

final class ReflectionReaderTest extends TestCase
{
    public function test_it_can_read_methods_off_an_interface(): void
    {
        require_once dirname(__FILE__, 2) . '/assets/BasicInterface.php';
        $reader = new ReflectionReader();

        self::assertSame(
            [
                'public function empty_method()',
                'public function untyped_method($argument)',
                'public function method(string $param_one, ?array $param_two = []): void',
                'public function variadic_method(string ...$params): string',
                'public static function static_function(): \\Acme\\SomeType'
            ],
            array_map(
                static fn(Method $method): string => (string) $method,
                $reader->getMethods(BasicInterface::class),
            ),
        );
    }

    public function test_it_can_read_inherited_methods(): void
    {
        require_once dirname(__FILE__, 2) . '/assets/BasicInterface.php';
        require_once dirname(__FILE__, 2) . '/assets/ExtendingInterface.php';

        $reader = new ReflectionReader();

        self::assertSame(
            [
                'public function another_method(array $argument_one, string ...$argument_two): void',
                'public function empty_method()',
                'public function untyped_method($argument)',
                'public function method(string $param_one, ?array $param_two = []): void',
                'public function variadic_method(string ...$params): string',
                'public static function static_function(): \\Acme\\SomeType'
            ],
            array_map(
                static fn(Method $method): string => (string) $method,
                $reader->getMethods(ExtendingInterface::class),
            ),
        );
    }

    public function test_it_can_read_abstract_classes(): void
    {
        require_once dirname(__FILE__, 2) . '/assets/AbstractClass.php';
        $reader = new ReflectionReader();

        self::assertSame(
            [
                'protected function do_action(string $input): void',
            ],
            array_map(
                static fn(Method $method): string => (string) $method,
                $reader->getMethods(AbstractClass::class),
            ),
        );
    }
}
