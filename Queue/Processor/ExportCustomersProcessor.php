<?php

namespace Lof\Mautic\Queue\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Event\ManagerInterface;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Helper\Data;

/**
 * Class ExportCustomersProcessor
 */
class ExportCustomersProcessor extends AbstractQueueProcessor
{
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
        parent::__construct($mauticContact, $helperData);
    }

    /**
     * @return void
     */
    public function process()
    {
        $this->mauticContact->export();
        return;

    }
}
