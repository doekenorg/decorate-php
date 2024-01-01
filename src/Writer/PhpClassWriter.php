<?php

namespace DoekeNorg\Decreator\Writer;

final class PhpClassWriter
{
    public function __construct(private readonly PathResolver $path_resolver)
    {
    }

    public function writeClass(string $class_name, string $content, bool $overwrite = false): void
    {
        if (!$path = $this->path_resolver->resolveFromClass($class_name)) {
            throw CouldNotWriteFile::becausePathCouldNotBeResolved();
        }

        if (!$overwrite && file_exists($path)) {
            throw CouldNotWriteFile::becauseFileAlreadyExists();
        }

        $this->ensureTargetDirectoryExists($path);

        if (file_put_contents($path, $content) === false) {
            CouldNotWriteFile::becauseThePathCouldNotBeWritten();
        }
    }

    private function ensureTargetDirectoryExists(string $path): void
    {
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        array_pop($parts);
        $path = implode(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR;

        if (!file_exists($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            CouldNotWriteFile::becauseThePathCouldNotBeWritten();
        }
    }
}
