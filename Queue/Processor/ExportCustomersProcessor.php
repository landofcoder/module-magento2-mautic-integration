<?php

namespace Lof\Mautic\Queue\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Event\ManagerInterface;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Helper\Data;
use Lof\Mautic\Queue\MessageQueues\Customer\Publisher;

/**
 * Class ExportCustomersProcessor
 */
class ExportCustomersProcessor extends AbstractQueueProcessor
{
    /**
    * @var Publisher
    */
    private $publisher;

    /**
     * CategoryImport constructor.
     *
     * @param Contact $mauticContact
     * @param Data $helperData
     * @param Publisher $publisher
     */
    public function __construct(
        Contact $mauticContact,
        Data $helperData,
        Publisher $publisher
    ) {
        parent::__construct($mauticContact, $helperData);
        $this->publisher = $publisher;
    }

    /**
     * @return void
     */
    public function process()
    {
        $collection = $this->mauticContact->getCustomerCollection();
        try {
            foreach ($collection as $customer) {
                if (!$this->helperData->isAyncApi()) {
                    $this->mauticContact->exportCustomer($customer);
                } else {
                    $data = $this->mauticContact->getRequestData([], $customer);
                    $this->publisher->execute(
                        $this->helperData->encodeData($data)
                    );
                }
            }
        } catch (\Exception $e) {
            //log exception at here
        }
        return;
    }
}
