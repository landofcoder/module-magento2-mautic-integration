<?php declare(strict_types=1);

namespace Lof\Mautic\Model\Mautic;

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
     * @var string|int
     */
    protected $_responseContactId = 0;


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
     * get Customer Collection
     *
     * @return mixed|object|array|null
     */
    public function getCustomerCollection()
    {
        $collection = $this->customerFactory->create()->getCollection()
            ->addAttributeToSelect('*');
        return $collection;
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
        $data = $this->getRequestData($customData, $customer);
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
     * Export contacts
     *
     * @param array|null $data
     * @return bool
     */
    public function exportContact($data = [])
    {
        $data = $this->getRequestData($data);
        if (isset($data['mautic_contact_id']) && (int)$data['mautic_contact_id']) {
            $mautic_contact_id = (int)$data['mautic_contact_id'];
            unset($data['mautic_contact_id']);
            $response = $this->getCurrentMauticApi()->edit($mautic_contact_id, $data);
        } else if (isset($data['email']) && $data['email']) {
            $response = $this->getCurrentMauticApi()->create($data);
        } else {
            return false;
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
     * Get response contact id
     *
     * @return string|int
     */
    public function getResponseContactId()
    {
        return $this->_responseContactId;
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
            $returnData = $address->getData();
            $returnData["country_id"] = $country;
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
     * Mapping contact data to mautic data
     * @param array
     * @return array
     */
    public function mappingContactData(array $data = [])
    {
        $helper = $this->mauticModel->getHelperData();
        $mappingFields = $helper->getMappingFields();
        if ($mappingFields) {
            foreach ($mappingFields as $row) {
                $mautic_field_id = isset($row["mautic_field_id"]) ? $row["mautic_field_id"] : "";
                $magento_customer_fields = isset($row["magento_customer_fields"]) ? $row["magento_customer_fields"] : "";
                if ($mautic_field_id && isset($data[$magento_customer_fields])) {
                    switch ($magento_customer_fields) {
                        case "website_id":
                            $data[$mautic_field_id] = $helper->getWebsiteBaseUrl((int)$data[$magento_customer_fields]);
                            break;
                        default:
                            $data[$mautic_field_id] = $data[$magento_customer_fields];
                            break;
                    }

                }
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestData(array $data = [], $customer = null)
    {
        if ($customer) {
            try {
                $customerData = $customer->getData();
            } catch (\Exception $e) {
                $customerData = $customer->__toArray();
            }
            $address = $this->_getCustomerAddress($customer);
            if ($address) {
                $customerData = array_merge($data, $address);
            }
            $data = is_array($data) ? $data : [];
            $data = array_merge($customerData, $data);
        }
        $helper = $this->mauticModel->getHelperData();
        $tags = [];
        if (!isset($data["tags"]) || !$data["tags"]) {
            $tags = $helper->getDefaultTags();
        } else {
            $tags = explode(",", $data["tags"]);
            $tags = array_merge($tags, $helper->getDefaultTags());
        }
        $data["tags"] = implode(",", $tags);
        $stage = isset($contact['stage']) && $contact['stage'] ? $contact['stage']: "";
        $convertStages = null;
        if ($stage) {
            $convertStages = $this->mauticModel->unSerializeData($stage);
            $data["stage"] = $convertStages;
        } else {
            unset($data["stage"]);
        }

        $data = $this->mappingContactData($data);//Mapping magento customer attributes (include address fields) to Mautic fields

        if (isset($data["customer_id"])) {
            unset($data["customer_id"]);
        }
        if (isset($data["contact_id"])) {
            unset($data["contact_id"]);
        }
        if (isset($data["created_at"])) {
            unset($data["created_at"]);
        }
        if (isset($data["updated_at"])) {
            unset($data["updated_at"]);
        }
        if (isset($data["password_hash"])) {
            unset($data["password_hash"]);
        }

        return $data;
    }

    /**
     * create contact on magento table
     * @param array|mixed $contact
     * @return Object|mixed|boolean
     */
    public function createContact($contact = [])
    {
        if (isset($contact['fields']) && isset($contact['fields']['all']) && $contact['id']) {
            $contactFields = $contact['fields']['all'];
            $contactItem = $this->contactFactory->create()->getCollection()
                                ->addFieldToFilter("mautic_contact_id", $contact['id'])
                                ->getFirstItem();
            $contactId = 0;
            if($contactItem) {
                $model = $this->contactFactory->create()->load($contactItem->getContactId());
                $contactId = $model->getId();
                $this->_responseContactId = $contactId;
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
            if ($customerModel && $customerModel->getId()) {
                $data['customer_id'] = $customerModel->getId();

                $model->setData($data);
                try {
                    $model->save();
                } catch (\Exception $e) {
                    //log exception at here
                    //echo $e->getMessage();
                }
                return $model;

            }
        }
        return false;
    }

}
