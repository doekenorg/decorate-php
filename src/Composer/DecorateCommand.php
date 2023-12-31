<?php

declare(strict_types=1);

namespace DoekeNorg\Decreator\Composer;

use Composer\Command\BaseCommand;
use Composer\Console\Input\InputArgument;
use Composer\Console\Input\InputOption;
use DoekeNorg\Decreator\Renderer;
use DoekeNorg\Decreator\Reader\ReflectionReader;
use DoekeNorg\Decreator\Request;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DecorateCommand extends BaseCommand
{
    private array $config;

    protected function configure(): void
    {
        $this->setName('decorate');
        $this->setDescription('Decorate an interface or abstract class');
        $this->addArgument('base-class', InputArgument::REQUIRED, 'The class to decorate.');
        $this->addArgument('destination-class', InputArgument::REQUIRED, 'The full classname destination.');
        $this->addArgument('variable', InputArgument::OPTIONAL, 'The inner variable name.');
        $this->addOption(
            'abstract',
            'a',
            InputOption::VALUE_NONE,
            'Whether the decorator should be created as abstract.'
        );
        $this->addOption('final', 'f', InputOption::VALUE_NONE, 'Whether the decorator should be created as final.');
        $this->addOption(
            'output',
            'o',
            InputOption::VALUE_NONE,
            'Whether the code should be return, instead of created.'
        );
        $this->addOption(
            'spaces',
            's',
            InputOption::VALUE_OPTIONAL,
            'How many spaces to use. 4 is default, otherwise it uses tabs.',
            'empty',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $renderer = new Renderer(new ReflectionReader());

        $request = $this->createRequest($input);
        $result = $renderer->output($request);

        if ($input->getOption('output')) {
            $output->write($result);
        } else {
            // todo: write to file.
        }

        return self::SUCCESS;
    }

    private function createRequest(InputInterface $input): Request
    {
        $config = $this->getConfig();
        $base_class = $input->getArgument('base-class');
        $destination_class = $input->getArgument('destination-class');

        $request = match (true) {
            $input->getOption('final') => Request::asFinal($base_class, $destination_class),
            $input->getOption('abstract') => Request::asAbstract($base_class, $destination_class),
            default => new Request($base_class, $destination_class),
        };

        return $request
            ->withVariable($input->getArgument('variable') ?? $config['variable'] ?? 'inner')
            ->withSpaces($this->getSpaces($input));
    }

    private function getConfig(): array
    {
        if (!isset($this->config)) {
            $this->config = [];
            if (!$composer = $this->tryComposer()) {
                return $this->config;
            }

            $global_composer_file = file_get_contents($composer->getConfig()->get('home') . '/composer.json');
            if (is_string($global_composer_file)) {
                $global_composer_json = json_decode($global_composer_file, true);
                $this->config = $global_composer_json['extra']['decorate'] ?? [];
            }
        }

        return $this->config;
    }

    private function getSpaces(InputInterface $input): int
    {
        $config = $this->getConfig();

        $spaces = $input->getOption('spaces');

        // If `--spaces=` was provided, the output is an empty string.
        if ($spaces === '') {
            $spaces = null;
        }

        // If no `--spaces` was provided, we use the configured default, or tabs (0).
        if ($spaces === 'empty') {
            $spaces = ($config['spaces'] ?? 0);
        }

        $spaces ??= $config['spaces'] ?? 4;

        return (int) $spaces;
    }
}
