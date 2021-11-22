<?php

namespace Lof\Mautic\Observer;

use Magento\Framework\Event\ObserverInterface;
use Lof\Mautic\Model\Mautic\AbstractApi;
use Lof\Mautic\Queue\MessageQueues\Order\Publisher;

class FrontendCheckoutSuccess implements ObserverInterface
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
        $order = $observer->getOrder();

        if (!$this->helper->isEnabled($order->getStoreId())) return $this;

        if ($order && $order->getId() && $this->helper->isCustomerIntegrationEnabled($order->getStoreId())) {
            $attribution = (float)$order->getSubtotal();
            $data = [
                "email" => $order->getCustomerEmail(),
                "firstname" => $order->getCustomerFirstname(),
                "lastname" => $order->getCustomerLastname(),
                "haspurchased" => true,
                "attribution" => $attribution,
                "tags" => "ordered"
            ];
            $address = $this->_getBillingAddress($order);
            if ($address) {
                $data = array_merge($data, $address);
            }
            if (!$this->helper->isAyncApi($order->getStoreId())) {
                $this->customerContact->exportContact($data);
            } else {
                $this->publisher->execute(
                    $this->helper->encodeData($data)
                );
            }
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
            $street = $address->getStreet();
            return array(
                AbstractApi::MAUTIC_CUSTOMER_ADRESS1 => is_array($street) ? implode(", ", $street) : $street,
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
