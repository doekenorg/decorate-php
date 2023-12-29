<?php

require_once __DIR__ . '/vendor/autoload.php';

$reader = new \DoekeNorg\Decreator\Reader\ReflectionReader();
$output = new \DoekeNorg\Decreator\Renderer($reader);

echo $output->output(\Psr\Container\ContainerInterface::class, 'TestClass');
