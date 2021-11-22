<?php

namespace Lof\Mautic\Observer;

use Magento\Framework\Event\ObserverInterface;
use Lof\Mautic\Queue\MessageQueues\Customer\Publisher;

class CustomerSaveAfter implements ObserverInterface
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
            if (!$this->helper->isAyncApi()) {
                $this->customerContact->exportCustomer($customer);
            } else {
                $data = $this->customerContact->getRequestData([], $customer);
                $this->publisher->execute(
                    $this->helper->encodeData($data)
                );
            }
        }
        return $this;
    }
}
