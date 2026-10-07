<?php

namespace Cke5\Console;

use Cke5\Handler\Cke5DatabaseHandler;
use Cke5\Handler\Cke5DefaultDataService;
use rex_console_command;
use rex_file;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Writes profiles, their styles, style groups and snippets to a bundle file —
 * the same format the export page produces and cke5:import reads back.
 */
class Cke5ExportCommand extends rex_console_command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Exports CKE5 profiles into a bundle file')
            ->addArgument('file', InputArgument::REQUIRED, 'Target file, e.g. redaxo/data/addons/project/cke5_profiles.json')
            ->addArgument('profiles', InputArgument::IS_ARRAY, 'Profile names to export; all profiles when omitted')
            ->addOption('pretty', null, InputOption::VALUE_NONE, 'Write the JSON indented');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getStyle($input, $output);

        $file = (string) $input->getArgument('file');
        /** @var array<int,string> $wanted */
        $wanted = $input->getArgument('profiles');

        $ids = [];
        $missing = $wanted;

        foreach (Cke5DatabaseHandler::getAllProfiles() ?: [] as $profile) {
            $name = isset($profile['name']) ? trim((string) $profile['name']) : '';

            if ($name === '') {
                continue;
            }

            if ($wanted !== [] && !in_array($name, $wanted, true)) {
                continue;
            }

            $ids[] = (int) ($profile['id'] ?? 0);
            $missing = array_diff($missing, [$name]);
        }

        if ($missing !== []) {
            $io->error(sprintf('Unknown profile(s): %s', implode(', ', $missing)));

            return self::FAILURE;
        }

        if ($ids === []) {
            $io->error('No profiles to export.');

            return self::FAILURE;
        }

        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        if ($input->getOption('pretty')) {
            $flags |= JSON_PRETTY_PRINT;
        }

        $bundle = Cke5DefaultDataService::exportBundle($ids);
        $json = json_encode($bundle, $flags);

        if (!is_string($json) || !rex_file::put($file, $json . "\n")) {
            $io->error(sprintf('Could not write "%s".', $file));

            return self::FAILURE;
        }

        $io->success(sprintf('Exported %d profile(s) to "%s".', count($bundle['profiles']), $file));

        return self::SUCCESS;
    }
}
