<?php

namespace Cke5\Console;

use Cke5\Creator\Cke5ProfilesCreator;
use Cke5\Handler\Cke5DatabaseHandler;
use Cke5\Handler\Cke5DefaultDataService;
use rex_console_command;
use rex_file;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Reads a bundle file written by cke5:export or the export page.
 *
 * A profile that already exists is left alone unless its name is passed with
 * --overwrite — same rule as the import page, so a bundle cannot silently
 * replace a profile someone configured on the target installation.
 */
class Cke5ImportCommand extends rex_console_command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Imports CKE5 profiles from a bundle file')
            ->addArgument('file', InputArgument::REQUIRED, 'Bundle file to read')
            ->addOption('overwrite', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Name of a profile that may be replaced')
            ->addOption('overwrite-all', null, InputOption::VALUE_NONE, 'Replace every profile the bundle contains');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getStyle($input, $output);

        $file = (string) $input->getArgument('file');

        if (!is_file($file)) {
            $io->error(sprintf('Bundle "%s" does not exist.', $file));

            return self::FAILURE;
        }

        $content = rex_file::get($file);
        $data = is_string($content) ? json_decode($content, true) : null;

        if (!is_array($data) || !isset($data['profiles']) || !is_array($data['profiles'])) {
            $io->error(sprintf('"%s" is not a CKE5 bundle.', $file));

            return self::FAILURE;
        }

        $names = [];

        foreach ($data['profiles'] as $profile) {
            $name = is_array($profile) && isset($profile['name']) ? trim((string) $profile['name']) : '';

            if ($name !== '') {
                $names[] = $name;
            }
        }

        if ($names === []) {
            $io->error('The bundle contains no profile.');

            return self::FAILURE;
        }

        /** @var array<int,string> $overwrite */
        $overwrite = $input->getOption('overwrite-all') ? $names : $input->getOption('overwrite');

        $skipped = [];

        foreach ($names as $name) {
            if (!in_array($name, $overwrite, true) && is_array(Cke5DatabaseHandler::loadProfile($name))) {
                $skipped[] = $name;
            }
        }

        // importBundle logs its exceptions and returns nothing, so the result
        // is read back from the database below.
        Cke5DefaultDataService::importBundle($file, $overwrite);

        $failed = [];

        foreach ($names as $name) {
            if (!is_array(Cke5DatabaseHandler::loadProfile($name))) {
                $failed[] = $name;
            }
        }

        if ($failed !== []) {
            $io->error(sprintf('Could not import: %s — see the REDAXO log.', implode(', ', $failed)));

            return self::FAILURE;
        }

        // The editor reads its profiles from the generated cke5profiles.js, not
        // from the database; without this they keep their old definition.
        Cke5ProfilesCreator::profilesCreate();

        if ($skipped !== []) {
            $io->note(sprintf('Kept existing profile(s): %s — pass --overwrite to replace.', implode(', ', $skipped)));
        }

        $io->success(sprintf('Imported %d profile(s) from "%s".', count($names) - count($skipped), $file));

        return self::SUCCESS;
    }
}
