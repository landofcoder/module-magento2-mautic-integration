<?php

namespace Lof\Mautic\Queue\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Event\ManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Helper\Data;
use Lof\Mautic\Queue\MessageQueues\Subscriber\Publisher;

/**
 * Class ExportSubscribersProcessor
 */
class ExportSubscribersProcessor extends AbstractQueueProcessor
{
    /**
    * @var Publisher|null
    */
    private $publisher = null;
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
     * @param Publisher $publisher
     */
    public function __construct(
        Contact $mauticContact,
        Data $helperData,
        SubscriberFactory $subscriberFactory,
        Publisher $publisher
    ) {
        parent::__construct($mauticContact, $helperData);
        $this->subscriberFactory = $subscriberFactory;
        $this->publisher = $publisher;
    }

    /**
     * @return void
     */
    public function process()
    {
        $subscribers = $this->subscriberFactory->create()->getCollection();
        try {
            foreach ($subscribers as $subscriber) {
                if ($this->helperData->isEnabled($subscriber->getStoreId())) {
                    $tags = ["newsletter"];
                    if ($subscriber->getSubscriberStatus() == 1) {
                        $tags[] = ["subscribed"];
                    } else {
                        $tags[] = ["unsubscribed"];
                    }
                    $subscriberData = [
                        "email" => $subscriber->getSubscriberEmail(),
                        "tags" => implode(",", $tags)
                    ];
                    if (!$this->helperData->isAyncApi($subscriber->getStoreId())) {
                        $this->mauticContact->exportContact($subscriberData);
                    } else {
                        $this->publisher->execute(
                            json_encode($subscriberData)
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
