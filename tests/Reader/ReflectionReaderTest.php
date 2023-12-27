<?php

namespace DoekeNorg\Decreator\Tests\Reader;

use Acme\BasicInterface;
use Acme\ExtendingInterface;
use DoekeNorg\Decreator\Reader\Method;
use DoekeNorg\Decreator\Reader\ReflectionReader;
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
                'public static function static_function(): Acme\\SomeType'
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
                'public static function static_function(): Acme\\SomeType'
            ],
            array_map(
                static fn(Method $method): string => (string) $method,
                $reader->getMethods(ExtendingInterface::class),
            ),
        );
    }
}
