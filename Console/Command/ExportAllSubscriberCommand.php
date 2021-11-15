<?php

namespace Lof\Mautic\Console\Command;

use Lof\Mautic\Queue\Processor\ContactQueueProcessorFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Registry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExportAllSubscriberCommand
 */
class ExportAllSubscriberCommand extends Command
{
    /**
     * @var ContactQueueProcessorFactory
     */
    private $contactProcessorFactory;
    /**
     * @var State
     */
    private $state;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * CategoryImport constructor.
     *
     * @param ContactQueueProcessorFactory $contactProcessorFactory
     * @param State $state
     * @param Registry $registry
     * @param null $name
     */
    public function __construct(
        ContactQueueProcessorFactory $contactProcessorFactory,
        State $state,
        Registry $registry,
        $name = null
    ) {
        parent::__construct($name);

        $this->contactProcessorFactory = $contactProcessorFactory;
        $this->state = $state;
        $this->registry = $registry;
    }

    /**
     * Configures the current command.a
     *
     * @return void
     */
    public function configure()
    {
        $this->setName('lofmautic:export:subscribers');
        $this->setDescription('Process export all contacts Data from Subscribers to Mautic');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);
        } catch (\Exception $ex) {
            // fail gracefully
        }

        $this->registry->register('isSecureArea', true);

        $start = $this->getCurrentMs();

        $output->writeln('<info>Initialization processing of contacts queue.</info>');
        $output->writeln(sprintf('<info>Started at %s</info>', (new \DateTime())->format('Y-m-d H:i:s')));
        $output->writeln('Processing...');

        $contactQueueProcessor = $this->contactProcessorFactory->create();

        $contactQueueProcessor->process();

        $end = $this->getCurrentMs();

        $output->writeln(sprintf('<info>Finished at %s</info>', (new \DateTime())->format('Y-m-d H:i:s')));
        $output->writeln(sprintf('<info>Total execution time %sms</info>', $end - $start));

        return 0;
    }

    /**
     *
     * @return float|int
     */
    private function getCurrentMs()
    {
        $mt = explode(' ', microtime());

        return ((int) $mt[1]) * 1000 + ((int) round($mt[0] * 1000));
    }
}
