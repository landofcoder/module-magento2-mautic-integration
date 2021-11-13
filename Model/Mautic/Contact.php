<?php

namespace Lof\Mautic\Model\Mautic;

use Mautic\Auth\ApiAuth;
use Mautic\MauticApi;
use Lof\Mautic\Model\Config\Source\OauthVersion;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Contact extends AbstractApi
{
    /**
     * Mautic address 1 field
     */
    const MAUTIC_CUSTOMER_ADRESS1 = 'address1';

    /**
     * Mautic address 2 field
     */
    const MAUTIC_CUSTOMER_ADRESS2 = 'address2';

    /**
     * Mautic postcode field
     */
    const MAUTIC_CUSTOMER_ZIPCODE = 'zipcode';

    /**
     * Mautic country field
     */
    const MAUTIC_CUSTOMER_COUNTRY = 'country';

    /**
     * Mautic region field
     */
    const MAUTIC_CUSTOMER_STATE = 'state';

    /**
     * Mautic city field
     */
    const MAUTIC_CUSTOMER_CITY = 'city';

    /**
     * Mautic company field
     */
    const MAUTIC_CUSTOMER_COMPANY = 'company';

    /**
     * Mautic phone field
     */
    const MAUTIC_CUSTOMER_PHONE = 'phone';

    /**
     * @var \Mautic\Api\Api
     */
    protected $_contactApi;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Lof\Mautic\Model\Mautic
     */
    protected $mauticModel;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Lof\Mautic\Model\ContactFactory
     */
    protected $contactFactory;


    /**
     * Initialize resource model
     *
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Lof\Mautic\Model\Mautic $mauticModel
     * @param \Lof\Mautic\Model\ContactFactory $contactFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Lof\Mautic\Model\Mautic $mauticModel,
        \Lof\Mautic\Model\ContactFactory $contactFactory
    ) {
        parent::__construct($context, $registry);
        $this->customerFactory = $customerFactory;
        $this->mauticModel = $mauticModel;
        $this->countryFactory = $countryFactory;
        $this->contactFactory = $contactFactory;
    }


    /**
     * @param string|int $id
     * @return array|mixed|bool
     */
    public function getItemById($id = "")
    {
        return [];

    }

    /**
     * @param string|int $id
     * @param array|mixed
     * @return array|mixed|bool
     */
    public function updateRecord($id, $data = [])
    {
        return [];

    }

    /**
     * @param string|int $id
     *
     * @return bool
     */
    public function deleteRecord($id)
    {
        return true;

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
     * get customer by email
     * 
     * @param string $email
     * @return mixed|Object|array|null
     */
    public function getCustomer($email)
    {
        $customer = $this->customerFactory->create()->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter("email", $email)
            ->getFirstItem();
 
        return $customer;
    }

    /**
     * Export contacts from customer
     *
     * @return array|mixed|string|null
     */
    public function getList($filter = "", $start = 0, $limit = 10, $orderBy = "", $orderByDir = "DESC")
    {
        $response = $this->_getContactApi()->getList($filter, $start, $limit, $orderBy, $orderByDir);
        return $response;
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
        if (isset($response['contact']) && $response['contact']) {
            $this->createContact($response['contact']);
        }

        return true;
    }

    /**
     * @return void
     */
    public function processSyncFromMautic()
    {
        $listContacts = $this->getList();
        if ($listContacts && isset($listContacts['contacts'])) {
            foreach ($listContacts['contacts'] as $contact) {
                $this->createContact($contact);
            }
        }
        return;

    }

    /**
     * create contact on magento table
     * @param array|mixed $contact
     * @return Object|mixed|boolean
     */
    public function createContact($contact = [])
    {
        if (isset($contact['fields']) && isset($contact['fields']['all'])) {
            $contactFields = $contact['fields']['all'];
            $model = $this->contactFactory->create()->load($contact['id'], "mautic_contact_id");
            $email = isset($contactFields['email']) ? $contactFields['email'] :'';
            $data = [
                "mautic_contact_id" => $contact['id'],
                "facebook" => isset($contactFields['facebook']) ? $contactFields['facebook'] :'',
                "foursquare" => isset($contactFields['foursquare']) ? $contactFields['foursquare'] :'',
                "instagram" => isset($contactFields['instagram']) ? $contactFields['instagram'] :'',
                "linkedin" => isset($contactFields['linkedin']) ? $contactFields['linkedin'] :'',
                "skype" => isset($contactFields['skype']) ? $contactFields['skype'] :'',
                "twitter" => isset($contactFields['twitter']) ? $contactFields['twitter'] :'',
                "website" => isset($contactFields['website']) ? $contactFields['website'] :''
            ];
            $tags = isset($contact['tags']) && $contact['tags'] ? $contact['tags']: [];
            $stage = isset($contact['stage']) && $contact['stage'] ? $contact['stage']: [];
            $convertStages = [];
            if ($stage) {
                $convertStages = [
                    "id" => $stage["id"],
                    "name" => $stage["name"],
                    "weight" => $stage["weight"]
                ];
            }
            $data['tags'] = $this->mauticModel->serializeData($tags);
            $data['stage'] = $this->mauticModel->serializeData($convertStages);

            $customerModel = $this->getCustomer($email);
            if ($customerModel->getId()) {
                $data['customer_id'] = $customerModel->getId();
                $model->setData($data);
                try {
                    $model->save();
                } catch (\Exception $e) {
                    //log exception at here
                }
                return $model;
            }
        }
        return false;
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
            $this->_contactApi = $mautic->getApi('contacts');
        }
        return $this->_contactApi;
    }
}
