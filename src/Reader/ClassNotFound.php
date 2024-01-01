<?php

namespace DoekeNorg\DecoratePhp\Reader;

use DoekeNorg\DecoratePhp\DecoratorException;

final class ClassNotFound extends DecoratorException
{
    public function __construct(
        string $message = 'Class could not be found.',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
