<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\Common\Contract\QueryBusInterface;
use App\Services\Import\Command\ImportCommand;
use App\Services\Import\Command\StreamFileCommand;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This command must import GitHub events.
 * You can add the parameters and code you want in this command to meet the need.
 */
class ImportGitHubEventsCommand extends Command
{
    protected static $defaultName = 'app:import-github-events';

    public function __construct(private QueryBusInterface $bus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import GH events');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $style->title('Start import');

        try {
            $style->info('Open File');
            $handle = $this->bus->query(new StreamFileCommand(
                \sprintf('https://data.gharchive.org/%s', \urlencode('2022-01-01-10.json.gz'))
            ));
            $this->bus->query(new ImportCommand($style, $handle));
        } catch (Exception $e) {
            $style->error($e->getMessage());

            return Command::FAILURE;
        }

        $style->success('Import finish');

        return Command::SUCCESS;
    }
}
