<?php

namespace Acme;

abstract class AbstractClass
{
    final public function final_method(string $input): void
    {
        $this->do_action($input);
    }

    abstract protected function do_action(string $input): void;
}