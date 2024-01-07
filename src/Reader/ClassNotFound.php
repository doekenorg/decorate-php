<?php

declare(strict_types=1);

namespace DoekeNorg\DecoratePhp\Reader;

use DoekeNorg\DecoratePhp\DecoratorException;

final class ClassNotFound extends DecoratorException
{
    public function __construct(
        string $class_name,
        string $message = 'Class "%s" could not be found.',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(sprintf($message, $class_name), $code, $previous);
    }
}
