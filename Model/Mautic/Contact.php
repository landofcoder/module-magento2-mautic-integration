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
     * @var string
     */
    protected $_api_type = "contacts";

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
        parent::__construct($context, $registry, $customerFactory, $countryFactory, $mauticModel );
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
     * Export customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param array|null $customData
     * @return bool
     */
    public function exportCustomer($customer, $customData = [])
    {
        $data = $customer->getData();
        $address = $this->_getCustomerAddress($customer);
        if ($address) {
            $data = array_merge($data, $address);
        }
        /**
         * Mapping mautic custom fields for customer custom attributes
         */
        $customFieldsMapping = $this->mappingCustomerCustomAttributes($customer);
        $data = array_merge($data, $customFieldsMapping);

        if ($customData) {
            $data = array_merge($data, $customData);
        }

        if (isset($data['mautic_contact_id']) && (int)$data['mautic_contact_id']) {
            $mautic_contact_id = (int)$data['mautic_contact_id'];
            unset($data['mautic_contact_id']);
            $response = $this->getCurrentMauticApi()->edit($mautic_contact_id, $data);
        } else {
            $response = $this->getCurrentMauticApi()->create($data);
        }

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
        $flag = false;
        if (isset($contact['fields']) && isset($contact['fields']['all']) && $contact['id']) {
            $contactFields = $contact['fields']['all'];
            $contactItem = $this->contactFactory->create()->getCollection()
                                ->addFieldToFilter("mautic_contact_id", $contact['id'])
                                ->getFirstItem();
            $contactId = 0;
            if($contactItem) {
                $model = $this->contactFactory->create()->load($contactItem->getContactId());
                $contactId = $model->getId();
                $flag = true;
            } else {
                $model = $this->contactFactory->create();
            }

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
            $data['contact_id'] = $contactId;
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
     * mapping customer custom attributes with mautic contact custom fields
     *
     * @param mixed|Object|array $customer
     * @return array
     */
    protected function mappingCustomerCustomAttributes($customer)
    {
        $dataMapping = [];
        //Get custom mapping fields from module config data
        return $dataMapping;
    }
}
