<?php

namespace DoekeNorg\Decreator\Tests\Reader;

use DoekeNorg\Decreator\Reader\Argument;
use DoekeNorg\Decreator\Reader\Arguments;
use DoekeNorg\Decreator\Reader\Method;
use DoekeNorg\Decreator\Reader\Visibility;
use PHPUnit\Framework\TestCase;

final class MethodTest extends TestCase
{
    public function test_it_can_be_cast_to_a_string(): void
    {
        $method = new Method(
            'someMethod',
            new Arguments(),
            Visibility::Public,
            'void',
        );

        self::assertSame('public function someMethod(): void', (string) $method);
    }

    public function test_it_can_have_arguments(): void
    {
        $method = new Method(
            'someMethod',
            new Arguments(
                new Argument('param', 'string'),
                new Argument('param_2', 'array', '[]'),
            ),
            Visibility::Public,
            'void',
        );

        self::assertSame('public function someMethod(string $param, array $param_2 = []): void', (string) $method);
    }
}
