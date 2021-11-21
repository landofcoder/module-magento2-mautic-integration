<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Mautic
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Mautic\Cron;

use Lof\Mautic\Queue\Processor\ExportSubscribersProcessorFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Registry;

/**
 * Class ExportContactsFromSubscriber
 *
 * @package Lof\Mautic\Cron
 */
class ExportContactsFromSubscriber
{
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
     * ExportContactsFromSubscriber constructor.
     *
     * @param ExportSubscribersProcessorFactory $subscriberProcessorFactory
     * @param State $state
     * @param Registry $registry
     */
    public function __construct(
        ExportSubscribersProcessorFactory $subscriberProcessorFactory,
        State $state,
        Registry $registry
    ) {
        $this->subscriberProcessorFactory = $subscriberProcessorFactory;
        $this->state = $state;
        $this->registry = $registry;
    }

    /**
     * Execute view action
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        try {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);
        } catch (\Exception $ex) {
            // fail gracefully
        }
        if (!$this->registry->registry('isSecureArea')) {
            $this->registry->register('isSecureArea', true);
        }
        $subscriberProcessor = $this->subscriberProcessorFactory->create();
        $subscriberProcessor->process();
    }
}
