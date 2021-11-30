<?php

namespace Lof\Mautic\Observer;

use Magento\Framework\Event\ObserverInterface;
use Lof\Mautic\Queue\MessageQueues\Review\Publisher;

class ReviewSaveAfter implements ObserverInterface
{
    /**
    * @var Publisher
    */
    private $publisher;

    /**
     * @var \Lof\Mautic\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\Mautic\Model\Mautic\Contact
     */
    protected $customerContact;

    /**
     * Construct customer save after observer
     *
     * @param \Lof\Mautic\Helper\Data $helper
     * @param \Lof\Mautic\Model\Mautic\Contact $customerContact
     * @param Publisher $publisher
     */
    public function __construct(
        \Lof\Mautic\Helper\Data $helper,
        \Lof\Mautic\Model\Mautic\Contact $customerContact,
        Publisher $publisher
    )
    {
        $this->helper = $helper;
        $this->customerContact = $customerContact;
        $this->publisher = $publisher;
    }

    /**
     * Sync customer data info to mautic
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $review = $observer->getReview();

        if (!$this->helper->isEnabled($review->getStoreId())) return $this;

        if ($review && $review->getCustomerId() && $this->helper->isCustomerIntegrationEnabled($review->getStoreId())) {
            $customer = $this->helper->getCustomerById($review->getCustomerId());
            $mauticPoints = 0;
            $mauticTags = [];
            if ($review->getCustomerId()) {
                $mauticPoints = $this->helper->getMauticPoint("review");
                $mauticTags = $this->helper->getMauticTags("review");
                $mauticTags = is_array($mauticTags) && $mauticTags ? $mauticTags : [];
            }
            $mauticTags[] = "reviews";
            $customData = [
                "firstname" => $review->getNickname(),
                "points" => (int)$mauticPoints,
                "tags" => implode(",", $mauticTags)
            ];
            if (!$this->helper->isAyncApi($review->getStoreId())) {
                $this->customerContact->exportCustomer($customer, $customData);
            } else {
                $data = $this->customerContact->getRequestData($customData, $customer);
                $this->publisher->execute(
                    $this->helper->encodeData($data)
                );
            }
        }
        return $this;
    }
}
