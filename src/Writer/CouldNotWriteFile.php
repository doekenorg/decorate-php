<?php

namespace DoekeNorg\Decreator\Writer;

use DoekeNorg\Decreator\DecoratorException;

final class CouldNotWriteFile extends DecoratorException
{
    public static function becausePathCouldNotBeResolved(string $message = 'Path could not be resolved.'): self
    {
        return new self($message);
    }

    public static function becauseFileAlreadyExists(string $message = 'File already exists.'): self
    {
        return new self($message);
    }

    public static function becauseThePathCouldNotBeWritten(string $message = 'File could not be written.'): self
    {
        return new self($message);
    }
}
