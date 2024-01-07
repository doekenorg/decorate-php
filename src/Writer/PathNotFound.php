<?php

declare(strict_types=1);

namespace DoekeNorg\DecoratePhp\Writer;

use DoekeNorg\DecoratePhp\DecoratorException;

final class PathNotFound extends DecoratorException
{
    public function __construct(
        string $message = 'Path could not be resolved.',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
