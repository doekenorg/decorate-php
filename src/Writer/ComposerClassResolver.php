<?php

namespace DoekeNorg\DecoratePhp\Writer;

use Composer\Composer;

final class ComposerClassResolver implements PathResolver
{
    public function __construct(private readonly Composer $composer)
    {
    }

    public function resolveFromClass(string $class_name): ?string
    {
        if ($psr_path = $this->resolvePsr4Class($class_name)) {
            return $psr_path;
        }

        throw new PathNotFound();
    }

    private function resolvePsr4Class(string $class_name): ?string
    {
        $autoload = $this->composer->getPackage()->getAutoload();
        if (!isset($autoload['psr-4'])) {
            return null;
        }

        $root_dir = dirname($this->composer->getConfig()->getConfigSource()->getName());

        foreach ($autoload['psr-4'] as $namespace => $path) {
            $path = rtrim($path, DIRECTORY_SEPARATOR);

            if (!str_starts_with($class_name, $namespace)) {
                continue;
            }

            $nested = ltrim($class_name, $namespace);
            $parts = explode('\\', $nested);
            $file = array_pop($parts) . '.php';

            return implode(DIRECTORY_SEPARATOR, [$root_dir, $path, ...$parts, $file]);
        }

        return null;
    }
}
