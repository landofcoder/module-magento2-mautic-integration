<?php

namespace Lof\Mautic\Observer;

use Magento\Framework\Event\ObserverInterface;
use Lof\Mautic\Queue\MessageQueues\Customer\Publisher;

class CustomerRegisterSuccess implements ObserverInterface
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
        if (!$this->helper->isEnabled()) return $this;

        $customer = $observer->getCustomer();
        if ($customer->getId() && $this->helper->isCustomerIntegrationEnabled()) {
            $mauticPoints = 0;
            $mauticTags = [];
            $mauticPoints = $this->helper->getMauticPoint("new_customer");
            $mauticTags = $this->helper->getMauticTags("new_customer");
            $mauticTags = is_array($mauticTags) && $mauticTags ? $mauticTags : [];

            $customData = [
                "email" => $customer->getEmail(),
                "firstname" => $customer->getFirstname(),
                "lastname" => $customer->getLastname(),
                "points" => (int)$mauticPoints,
                "tags" => implode(",", $mauticTags)
            ];
            if (!$this->helper->isAyncApi()) {
                $this->customerContact->exportContact($customData);
            } else {
                $this->publisher->execute(
                    $this->helper->encodeData($customData)
                );
            }
        }
        return $this;
    }
}
