<?php

namespace Lof\Mautic\Observer;

use Magento\Framework\Event\ObserverInterface;

class ReviewSaveAfter implements ObserverInterface
{
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
     */
    public function __construct(
        \Lof\Mautic\Helper\Data $helper,
        \Lof\Mautic\Model\Mautic\Contact $customerContact
    )
    {
        $this->helper = $helper;
        $this->customerContact = $customerContact;
    }

    /**
     * Sync customer data info to mautic
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) return $this;

        $review = $observer->getReview();
        if ($review && $review->getCustomerId() && $this->helper->isCustomerIntegrationEnabled()) {
            $customer = $this->helper->getCustomerById($review->getCustomerId());
            $customData = [
                "firstname" => $review->getNickname(),
                "tags" => "reviews"
            ];
            $this->customerContact->exportCustomer($customer, $customData);
        }
        return $this;
    }
}
