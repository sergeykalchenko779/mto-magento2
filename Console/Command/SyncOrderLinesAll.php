<?php

namespace Maatoo\Maatoo\Console\Command;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Maatoo\Maatoo\Model\Synchronization\OrderLinesAll;

class SyncOrderLinesAll extends Command
{
    /**
     * @var OrderLinesAll
     */
    private $sync;
    /**
     * @var State
     */
    private $state;

    public function __construct(
        OrderLinesAll $sync,
        State $state,
        string $name = null
    ) {
        $this->sync = $sync;
        $this->state = $state;
        parent::__construct($name);
    }

    /**
     * Configure the command line
     */
    protected function configure()
    {
        $this->setName('maatoo:sync:order_lines:all')
            ->setDescription(__('Maatoo synchronization'))
            ->setDefinition([]);
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->emulateAreaCode(
            Area::AREA_ADMINHTML,
            [$this, 'generate'],
            [$input, $output]
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function generate(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Maatoo synchronization all order lines started.</info>');
        $this->sync->sync(
            function($message) use($output) {
                $output->writeln('<info>' . $message . '</info>');
            }
        );
        $output->writeln('<info>Maatoo synchronization all order lines finished.</info>');

        return Cli::RETURN_SUCCESS;
    }
}
