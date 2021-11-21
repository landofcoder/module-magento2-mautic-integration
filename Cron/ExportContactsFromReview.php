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

use Lof\Mautic\Queue\Processor\ExportReviewsProcessorFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Registry;

/**
 * Class ExportContactsFromReview
 *
 * @package Lof\Mautic\Cron
 */
class ExportContactsFromReview
{
    /**
     * @var ExportReviewsProcessorFactory
     */
    private $reviewProcessorFactory;
    /**
     * @var State
     */
    private $state;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * ExportContactsFromCustomer constructor.
     *
     * @param ExportReviewsProcessorFactory $reviewProcessorFactory
     * @param State $state
     * @param Registry $registry
     */
    public function __construct(
        ExportReviewsProcessorFactory $reviewProcessorFactory,
        State $state,
        Registry $registry
    ) {
        $this->reviewProcessorFactory = $reviewProcessorFactory;
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
        $reviewProcessor = $this->reviewProcessorFactory->create();
        $reviewProcessor->process();
    }
}
