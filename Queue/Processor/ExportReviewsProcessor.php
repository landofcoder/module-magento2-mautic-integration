<?php

namespace Lof\Mautic\Queue\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Event\ManagerInterface;
use Magento\Review\Model\ReviewFactory;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Helper\Data;
use Lof\Mautic\Queue\MessageQueues\Review\Publisher;

/**
 * Class ExportReviewsProcessor
 */
class ExportReviewsProcessor extends AbstractQueueProcessor
{
    /**
    * @var Publisher
    */
    private $publisher;

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
     * @param Publisher $publisher
     */
    public function __construct(
        Contact $mauticContact,
        Data $helperData,
        ReviewFactory $reviewFactory,
        Publisher $publisher
    ) {
        parent::__construct($mauticContact, $helperData);
        $this->reviewFactory = $reviewFactory;
        $this->publisher = $publisher;
    }

    /**
     * @return void
     */
    public function process()
    {
        $collection = $this->reviewFactory->create()->getCollection()
                                ->addFieldToFilter("customer_id", ['notnull' => true])
                                ->addFieldToFilter("customer_id", ['neq' => 'NULL']);
        $collection->getSelect()->group('customer_id');

        try {
            foreach ($collection as $item) {
                if ($item->getCustomerId()) {
                    $customer = $this->helperData->getCustomerById($item->getCustomerId());
                    $customData = [
                        "firstname" => $item->getNickname(),
                        "tags" => "reviews"
                    ];
                    if (!$this->helperData->isAyncApi()) {
                        $this->mauticContact->exportCustomer($customer, $customData);
                    } else {
                        $data = $this->mauticContact->getRequestData($customData, $customer);
                        $this->publisher->execute(
                            $this->helperData->encodeData($data)
                        );
                    }

                }
            }
        } catch (\Exception $e) {
            //log exception at here
        }
        return;

    }
}
