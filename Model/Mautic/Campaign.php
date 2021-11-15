<?php

namespace Lof\Mautic\Model\Mautic;

use Mautic\Auth\ApiAuth;
use Mautic\MauticApi;
use Lof\Mautic\Model\Config\Source\OauthVersion;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Campaign extends AbstractApi
{
    /**
     * @var string
     */
    protected $_api_type = "campaigns";

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;


    /**
     * Initialize resource model
     *
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Lof\Mautic\Model\Mautic $mauticModel
    ) {
        parent::__construct($context, $registry, $customerFactory, $countryFactory, $mauticModel );
        $this->countryFactory = $countryFactory;
    }

    /**
     * Export contacts from customer
     *
     * @return bool
     */
    public function export()
    {
        $customers = $this->customerFactory->create()->getCollection()
            ->addAttributeToSelect('*');

        foreach ($customers as $customer) {
            $this->exportCustomer($customer);
        }

        return true;
    }

    /**
     * Export customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return bool
     */
    public function exportCustomer($customer)
    {
        $data = $customer->getData();
        $address = $this->_getCustomerAddress($customer);
        if ($address) {
            $data = array_merge($data, $address);
        }
        $response = $this->_getContactApi()->create($data);

        if (isset($response['errors']) && count($response['errors'])) {
            $this->mauticModel
                ->executeErrorResponse($response);

            return false;
        }

        return true;
    }

    /**
     * Retrieve customer address
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return array|mixed|bool
     */
    protected function _getCustomerAddress($customer)
    {
        $address = false;
        if ($customer->getPrimaryBillingAddress()) {
            $address = $customer->getPrimaryBillingAddress();
        } elseif ($customer->getPrimaryShippingAddress()) {
            $address = $customer->getPrimaryShippingAddress();
        } elseif ($customer->getAddresses()) {
            $addresses = $customer->getAddresses();
            $address = array_shift($addresses);
        }

        if ($address) {

            $country = $this->countryFactory->create()->loadByCode($address->getCountry());

            return array(
                self::MAUTIC_CUSTOMER_ADRESS1 => $address->getStreet1(),
                self::MAUTIC_CUSTOMER_ADRESS2 => $address->getStreet2(),
                self::MAUTIC_CUSTOMER_ZIPCODE => $address->getPostcode(),
                self::MAUTIC_CUSTOMER_COUNTRY => $country->getName(),
                self::MAUTIC_CUSTOMER_STATE => $address->getRegion(),
                self::MAUTIC_CUSTOMER_CITY => $address->getCity(),
                self::MAUTIC_CUSTOMER_COMPANY => $address->getCompany(),
                self::MAUTIC_CUSTOMER_PHONE => $address->getTelephone()
            );
        }

        return false;
    }

    /**
     * Retrieve contact api
     *
     * @return \Mautic\Api\Api
     */
    protected function _getContactApi()
    {
        if ($this->_contactApi == null) {
            $mautic = $this->mauticModel;
            $this->_contactApi = $mautic->getApi('campaigns');
        }
        return $this->_contactApi;
    }
}
