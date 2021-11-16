<?php

namespace Lof\Mautic\Queue\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Event\ManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Helper\Data;

/**
 * Class ExportSubscribersProcessor
 */
class ExportSubscribersProcessor extends AbstractQueueProcessor
{
    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * CategoryImport constructor.
     *
     * @param Contact $mauticContact
     * @param Data $helperData
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        Contact $mauticContact,
        Data $helperData,
        SubscriberFactory $subscriberFactory
    ) {
        parent::__construct($mauticContact, $helperData);
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * @return void
     */
    public function process()
    {
        $subscribers = $this->subscriberFactory->create()->getCollection()
                                ->addFieldToFilter("subscriber_status", 1);
        try {
            foreach ($subscribers as $subscriber) {
                $subscriberData = [
                    "email" => $subscriber->getSubscriberEmail()
                ];

                $this->mauticContact->exportContact($subscriberData);
            }
        } catch (\Exception $e) {
            //log exception at here
        }
        return;

    }
}
