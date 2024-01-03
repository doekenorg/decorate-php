<?php

namespace DoekeNorg\DecoratePhp\Tests;

use Acme\AbstractClass;
use Acme\ExtendingInterface;
use DoekeNorg\DecoratePhp\Reader\ReflectionReader;
use DoekeNorg\DecoratePhp\Renderer\PhpClassRenderer;
use DoekeNorg\DecoratePhp\Renderer\RenderRequest;
use PHPUnit\Framework\TestCase;

/**
 * Todo: This is an integration test. Move it over and make a Unit test.
 * Todo: test for spaces and tabs.
 */
final class RendererTest extends TestCase
{
    public function testOutput(): void
    {
        require_once __DIR__ . '/assets/classes/BasicInterface.php';
        require_once __DIR__ . '/assets/classes/ExtendingInterface.php';

        $output = new PhpClassRenderer(new ReflectionReader());
        $php = $output->render(new RenderRequest(ExtendingInterface::class, \Acme\DecoratingClass::class));
        $php = strtr($php, ['<?php' => '', '?>' => '']);


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
        require_once __DIR__ . '/assets/classes/AbstractClass.php';

        $output = new PhpClassRenderer(new ReflectionReader());
        $request = new RenderRequest(AbstractClass::class, \Acme\DecoratingAbstractClass::class);
        $php = $output->render($request->withoutPropertyPromotion());

        $php = strtr($php, ['<?php' => '', '?>' => '']);

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

        $class = new \Acme\DecoratingAbstractClass($mock, 'test');
        $class->final_method('test');
    }
}
