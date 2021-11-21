<?php

namespace Lof\Mautic\Observer;

use Magento\Framework\Event\ObserverInterface;
use Lof\Mautic\Queue\MessageQueues\Customer\Publisher;

class ContactSaveAfter implements ObserverInterface
{
    /**
    * @var Publisher
    */
    private $_publisher;

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
     * Sync Customer Contact to mautic
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) return $this;

        $contactModel = $observer->getContact();
        if ($contactModel->getContactId() && $this->helper->isCustomerIntegrationEnabled()) {
            $customer = $this->customerContact->getCustomerModel()->load((int)$contactModel->getCustomerId());
            if ($customer && $customer->getId()) {

                $customData = $contactModel->getData();
                unset($customData["customer_id"]);
                unset($customData["contact_id"]);
                unset($customData["created_at"]);
                unset($customData["updated_at"]);

                if (!$this->helper->isAyncApi()) {
                    $this->customerContact->exportCustomer($customer, $customData);
                } else {
                    $data = $this->customerContact->getRequestData($customData, $customer);
                    $this->publisher->execute(
                        $this->helper->encodeData($data)
                    );
                }
            }
        }
        return $this;
    }
}
