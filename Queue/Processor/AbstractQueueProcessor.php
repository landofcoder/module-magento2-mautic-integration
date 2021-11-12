<?php

namespace Lof\Mautic\Queue\Processor;

use Lof\Mautic\Queue\QueueProcessorInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class AbstractProcessor
 */
abstract class AbstractQueueProcessor implements QueueProcessorInterface
{
    /**
     * @return void
     */
    public function process()
    {
        return;

    }
}
