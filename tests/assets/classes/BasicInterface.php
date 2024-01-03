<?php

namespace Acme;

interface BasicInterface
{
    public function empty_method();

    function untyped_method($argument);

    public function method(string $param_one, ?array $param_two = []): void;

    public function variadic_method(string ...$params): string;

    public static function static_function(): SomeType;

}
