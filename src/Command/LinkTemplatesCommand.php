<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:link-templates',
    description: 'Link templates in public folder',
)]
class LinkTemplatesCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('clear-up', null, InputOption::VALUE_NONE, 'Clear-up templates before linking');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('clear-up')) {
            exec('ls /srv/app/public | grep -xv "index.php" | cd /srv/app/public && xargs rm -rf');
        }

        exec('cd /srv/app/public && ln -s ../templates/* .');

        return Command::SUCCESS;
    }
}
