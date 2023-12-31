<?php

namespace Acme;

interface ExtendingInterface extends BasicInterface
{
    public function another_method(array $argument_one, string ...$argument_two): void;
}
