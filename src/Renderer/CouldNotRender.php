<?php

namespace DoekeNorg\DecoratePhp\Renderer;

use DoekeNorg\DecoratePhp\DecoratorException;

final class CouldNotRender extends DecoratorException
{
    public function __construct(
        string $message = 'Could not render the class.',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
