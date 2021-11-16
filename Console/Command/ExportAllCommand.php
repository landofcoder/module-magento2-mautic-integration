<?php

namespace Lof\Mautic\Console\Command;

use Lof\Mautic\Queue\Processor\ExportCustomersProcessorFactory;
use Lof\Mautic\Queue\Processor\ExportReviewsProcessorFactory;
use Lof\Mautic\Queue\Processor\ContactQueueProcessorFactory;
use Lof\Mautic\Queue\Processor\ExportSubscribersProcessorFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Registry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExportAllCommand
 */
class ExportAllCommand extends Command
{
    /**
     * @var ExportReviewsProcessorFactory
     */
    private $reviewProcessorFactory;

    /**
     * @var ExportCustomersProcessorFactory
     */
    private $exportCustomerProcessorFactory;

    /**
     * @var ContactQueueProcessorFactory
     */
    private $contactProcessorFactory;

    /**
     * @var ExportSubscribersProcessorFactory
     */
    private $subscriberProcessorFactory;

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
     * @param ExportCustomersProcessorFactory $exportCustomerProcessorFactory
     * @param ExportReviewsProcessorFactory $reviewProcessorFactory
     * @param ExportOrdersProcessorFactory $exportOrdersProcessorFactory
     * @param ExportSubscribersProcessorFactory $subscriberProcessorFactory
     * @param State $state
     * @param Registry $registry
     * @param null $name
     */
    public function __construct(
        ExportCustomersProcessorFactory $exportCustomerProcessorFactory,
        ExportReviewsProcessorFactory $reviewProcessorFactory,
        ExportOrdersProcessorFactory $exportOrdersProcessorFactory,
        ExportSubscribersProcessorFactory $subscriberProcessorFactory,
        State $state,
        Registry $registry,
        $name = null
    ) {
        parent::__construct($name);

        $this->exportCustomerProcessorFactory = $exportCustomerProcessorFactory;
        $this->reviewProcessorFactory = $reviewProcessorFactory;
        $this->exportOrdersProcessorFactory = $exportOrdersProcessorFactory;
        $this->subscriberProcessorFactory = $subscriberProcessorFactory;
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
        $this->setName('lofmautic:export:all');
        $this->setDescription('Process export all contacts Data from all to Mautic');
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

        // Start export customers
        $this->executeCustomers($input, $output);

        // End Start export customers

        // Start export reviews
        $this->executeReviews($input, $output);

        // End Start export reviews

        // Start export subscribers
        $this->executeSubscribers($input, $output);

        // End Start export subscribers

        // Start export orders
        $this->executeOrders($input, $output);

        // End Start export orders

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     *
     */
    protected function executeCustomers(InputInterface $input, OutputInterface $output)
    {
        $start = $this->getCurrentMs();

        $output->writeln('<info>Initialization exporting of contacts of all.</info>');
        $output->writeln(sprintf('<info>Started at %s</info>', (new \DateTime())->format('Y-m-d H:i:s')));
        $output->writeln('Exporting...');

        $exportCustomerProcessor = $this->exportCustomerProcessorFactory->create();

        $exportCustomerProcessor->process();

        $end = $this->getCurrentMs();

        $output->writeln(sprintf('<info>Finished at %s</info>', (new \DateTime())->format('Y-m-d H:i:s')));
        $output->writeln(sprintf('<info>Total execution time %sms</info>', $end - $start));

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     *
     */
    protected function executeReviews(InputInterface $input, OutputInterface $output)
    {
        $start = $this->getCurrentMs();

        $output->writeln('<info>Initialization exporting of contacts in Reviews.</info>');
        $output->writeln(sprintf('<info>Started at %s</info>', (new \DateTime())->format('Y-m-d H:i:s')));
        $output->writeln('Exporting...');

        $reviewProcessor = $this->reviewProcessorFactory->create();

        $reviewProcessor->process();

        $end = $this->getCurrentMs();

        $output->writeln(sprintf('<info>Finished at %s</info>', (new \DateTime())->format('Y-m-d H:i:s')));
        $output->writeln(sprintf('<info>Total execution time %sms</info>', $end - $start));

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     *
     */
    protected function executeSubscribers(InputInterface $input, OutputInterface $output)
    {
        $start = $this->getCurrentMs();

        $output->writeln('<info>Initialization exporting of contacts in Subscribers.</info>');
        $output->writeln(sprintf('<info>Started at %s</info>', (new \DateTime())->format('Y-m-d H:i:s')));
        $output->writeln('Exporting...');

        $subscriberProcessor = $this->subscriberProcessorFactory->create();

        $subscriberProcessor->process();

        $end = $this->getCurrentMs();

        $output->writeln(sprintf('<info>Finished at %s</info>', (new \DateTime())->format('Y-m-d H:i:s')));
        $output->writeln(sprintf('<info>Total execution time %sms</info>', $end - $start));

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     *
     */
    protected function executeOrders(InputInterface $input, OutputInterface $output)
    {
        $start = $this->getCurrentMs();

        $output->writeln('<info>Initialization exporting of contacts in Orders.</info>');
        $output->writeln(sprintf('<info>Started at %s</info>', (new \DateTime())->format('Y-m-d H:i:s')));
        $output->writeln('Exporting...');

        $exportOrdersProcessor = $this->exportOrdersProcessorFactory->create();

        $exportOrdersProcessor->process();

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
