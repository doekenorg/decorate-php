<?php

namespace DoekeNorg\Decreator\Tests;

use Acme\ExtendingInterface;
use DoekeNorg\Decreator\Output;
use DoekeNorg\Decreator\Reader\ReflectionReader;
use PHPUnit\Framework\TestCase;

final class OutputTest extends TestCase
{
    public function testOutput()
    {
        require_once __DIR__ . '/assets/BasicInterface.php';
        require_once __DIR__ . '/assets/ExtendingInterface.php';

        $output = new Output(new ReflectionReader());
        $php = $output->output(ExtendingInterface::class, \Acme\Very\Deep\DecoratingClass::class);

        // Don't try this at home kids.
        (static function (string $php): void {
            eval($php);
        })($php);

        $mock = $this->createMock(ExtendingInterface::class);
        $mock->expects(self::once())
            ->method('variadic_method')
            ->with('one', 'two')
            ->willReturn('one-two');

        $class = new \Acme\Very\Deep\DecoratingClass($mock);

        self::assertSame('one-two', $class->variadic_method('one', 'two'));
    }
}
