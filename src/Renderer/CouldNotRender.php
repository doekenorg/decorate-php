<?php

namespace DoekeNorg\Decreator\Renderer;

use DoekeNorg\Decreator\DecoratorException;

final class CouldNotRender extends DecoratorException
{
    public function __construct(
        string $message = 'Could not render the class.',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
