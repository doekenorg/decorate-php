<?php

namespace DoekeNorg\Decreator\Tests;

use Acme\AbstractClass;
use Acme\ExtendingInterface;
use DoekeNorg\Decreator\Output;
use DoekeNorg\Decreator\Reader\ReflectionReader;
use PHPUnit\Framework\TestCase;

/**
 * Todo: This is an intergration test. Move it over and make a Unit test.
 * Todo: Clean up this test, it has too much duplication.
 */
final class OutputTest extends TestCase
{
    public function testOutput(): void
    {
        require_once __DIR__ . '/assets/BasicInterface.php';
        require_once __DIR__ . '/assets/ExtendingInterface.php';

        $output = new Output(new ReflectionReader());
        $php = $output->output(ExtendingInterface::class, \Acme\DecoratingClass::class);

        // Don't try this at home kids.
        (static function (string $php): void {
            eval($php);
        })(
            $php
        );

        $mock = $this->createMock(ExtendingInterface::class);
        $mock->expects(self::once())
            ->method('variadic_method')
            ->with('one', 'two')
            ->willReturn('one-two');

        $class = new \Acme\DecoratingClass($mock);

        self::assertSame('one-two', $class->variadic_method('one', 'two'));
    }

    public function testOutputAbstract(): void
    {
        require_once __DIR__ . '/assets/AbstractClass.php';

        $output = new Output(new ReflectionReader());
        $php = $output->output(AbstractClass::class, \Acme\DecoratingAbstractClass::class);

        // Don't try this at home kids.
        (static function (string $php): void {
            eval($php);
        })(
            $php
        );

        $mock = $this->createMock(AbstractClass::class);
        $mock->expects(self::once())
            ->method('do_action')
            ->with('test');

        $class = new \Acme\DecoratingAbstractClass($mock);
        $class->final_method('test');
    }
}
