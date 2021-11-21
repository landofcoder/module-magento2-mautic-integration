<?php

namespace Lof\Mautic\Queue\Processor;

use Lof\Mautic\Queue\QueueProcessorInterface;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Helper\Data;
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
     * @var Contact
     */
    protected $mauticContact;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * CategoryImport constructor.
     *
     * @param Contact $mauticContact
     * @param Data $helperData
     */
    public function __construct(
        Contact $mauticContact,
        Data $helperData
    ) {
        $this->helperData = $helperData;
        $this->mauticContact = $mauticContact;
    }

    /**
     * @return void
     */
    public function process()
    {
        return;
    }

    public function processMessage(string $_data)
    {
        // do something with message queue data
    }
}
