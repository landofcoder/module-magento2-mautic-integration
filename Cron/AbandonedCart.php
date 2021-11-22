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

use Lof\Mautic\Queue\Processor\AbandonedCartProcessorFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Registry;

/**
 * Class AbandonedCart
 *
 * @package Lof\Mautic\Cron
 */
class AbandonedCart
{
    /**
     * @var AbandonedCartProcessorFactory
     */
    private $abandonedCartProcessorFactory;
    /**
     * @var State
     */
    private $state;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * AbandonedCart constructor.
     *
     * @param AbandonedCartProcessorFactory $abandonedCartProcessorFactory
     * @param State $state
     * @param Registry $registry
     */
    public function __construct(
        AbandonedCartProcessorFactory $abandonedCartProcessorFactory,
        State $state,
        Registry $registry
    ) {
        $this->abandonedCartProcessorFactory = $abandonedCartProcessorFactory;
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
        $abandonedCartProcessor = $this->abandonedCartProcessorFactory->create();
        $abandonedCartProcessor->process();
    }
}
