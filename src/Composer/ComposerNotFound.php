<?php

namespace DoekeNorg\Decreator\Composer;

use DoekeNorg\Decreator\DecoratorException;

final class ComposerNotFound extends DecoratorException
{
    public function __construct(
        string $message = 'Composer could not be located.',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
