<?php

namespace Lof\Mautic\Queue\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Event\ManagerInterface;
use Magento\Review\Model\ReviewFactory;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Helper\Data;

/**
 * Class ExportReviewsProcessor
 */
class ExportReviewsProcessor extends AbstractQueueProcessor
{
    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * CategoryImport constructor.
     *
     * @param Contact $mauticContact
     * @param Data $helperData
     * @param ReviewFactory $reviewFactory
     */
    public function __construct(
        Contact $mauticContact,
        Data $helperData,
        ReviewFactory $reviewFactory
    ) {
        parent::__construct($mauticContact, $helperData);
        $this->reviewFactory = $reviewFactory;
    }

    /**
     * @return void
     */
    public function process()
    {
        $collection = $this->reviewFactory->create()->getCollection()
                                ->addFieldToFilter("customer_id", ['null' => false]);
        $collection->getSelect()->group('customer_id');

        try {
            foreach ($collection as $item) {
                $customer = $this->helperData->getCustomerById($item->getCustomerId());
                $customData = [
                    "firstname" => $item->getNickname()
                ];
                $this->mauticContact->exportCustomer($customer, $customData);
            }
        } catch (\Exception $e) {
            //log exception at here
        }
        return;

    }
}
