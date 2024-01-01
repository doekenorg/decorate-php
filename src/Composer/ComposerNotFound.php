<?php

namespace DoekeNorg\DecoratePhp\Composer;

use DoekeNorg\DecoratePhp\DecoratorException;

final class ComposerNotFound extends DecoratorException
{
    public function __construct(
        string $message = 'Composer could not be located.',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
