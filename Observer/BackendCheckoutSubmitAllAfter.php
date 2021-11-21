<?php

namespace Lof\Mautic\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Lof\Mautic\Model\Mautic\AbstractApi;

class BackendCheckoutSubmitAllAfter implements ObserverInterface
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

        $order = $observer->getOrder();
        if ($order && $order->getId() &&  $order->getState()== Order::STATE_NEW && $this->helper->isCustomerIntegrationEnabled()) {
            $data = [
                "email" => $order->getCustomerEmail(),
                "firstname" => $order->getCustomerFirstname(),
                "lastname" => $order->getCustomerLastname(),
                "haspurchased" => true,
                "tags" => "ordered"
            ];
            $address = $this->_getBillingAddress($order);
            if ($address) {
                $data = array_merge($data, $address);
            }
            $this->customerContact->exportContact($data);
        }
        return $this;
    }

    /**
     * Retrieve order billing address
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array|mixed|bool
     */
    protected function _getBillingAddress($order)
    {
        $address = $order->getBillingAddress();

        if ($address) {

            $country = $this->customerContact->getCountryModel()->loadByCode($address->getCountryId());

            return array(
                AbstractApi::MAUTIC_CUSTOMER_ADRESS1 => $address->getStreet(),
                AbstractApi::MAUTIC_CUSTOMER_ADRESS2 => "",
                AbstractApi::MAUTIC_CUSTOMER_ZIPCODE => $address->getPostcode(),
                AbstractApi::MAUTIC_CUSTOMER_COUNTRY => $country->getName(),
                AbstractApi::MAUTIC_CUSTOMER_STATE => $address->getRegion(),
                AbstractApi::MAUTIC_CUSTOMER_CITY => $address->getCity(),
                AbstractApi::MAUTIC_CUSTOMER_COMPANY => $address->getCompany(),
                AbstractApi::MAUTIC_CUSTOMER_PHONE => $address->getTelephone()
            );
        }
        return false;
    }
}
