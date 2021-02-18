<?php
declare(strict_types=1);

namespace App\Commands;

use App\Services\Interfaces\ParserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CurrencyImportCommand extends Command
{
    private ParserInterface $parser;

    protected function configure(): void
    {
        $this
            ->setName('import:currency')
            ->setDescription('Downloads Choice Content')
            ->setHelp('')
        ;
    }

    public function __construct(ParserInterface $parser)
    {
        parent::__construct();

        $this->parser = $parser;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start currency import');
        $this->parser->parse();
        $output->writeln("Finish currency import, values added: {$this->parser->getImportedValuesCount()}");

        return 0;
    }
}
