<?php

namespace Lof\Mautic\Model\Mautic;

use Mautic\Auth\ApiAuth;
use Mautic\MauticApi;
use Lof\Mautic\Model\Config\Source\OauthVersion;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

/**
 * Class AbstractApi
 */
abstract class AbstractApi extends \Magento\Framework\Model\AbstractModel
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
     * @var string
     */
    protected $_api_type = "contacts";

    /**
     * @var \Mautic\Api\Api[] | null
     */
    protected $_contactApi = [];

    /**
     * @var array|mixed|null
     */
    protected $_campaigns = null;

    /**
     * @var array|mixed|null
     */
    protected $_segments = null;

    /**
     * @var array|mixed|null
     */
    protected $_industries = null;

    /**
     * @var array|mixed|null
     */
    protected $_company_fields = null;

    /**
     * @var array|mixed|null
     */
    protected $_contact_fields = null;

    /**
     * @var array|mixed|null
     */
    protected $_tags = null;

    /**
     * @var array|mixed|null
     */
    protected $_stages = null;

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
     * Initialize resource model
     *
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Lof\Mautic\Model\Mautic $mauticModel
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Lof\Mautic\Model\Mautic $mauticModel
    ) {
        parent::__construct($context, $registry);
        $this->customerFactory = $customerFactory;
        $this->mauticModel = $mauticModel;
        $this->countryFactory = $countryFactory;
    }

    /**
     * Get customer model
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomerModel()
    {
        return $this->customerFactory->create();
    }

    /**
     * Get mautic model
     *
     * @return \Lof\Mautic\Model\Mautic
     */
    public function getMauticModel()
    {
        return $this->mauticModel;
    }

    /**
     * Get country model
     *
     * @return \Magento\Directory\Model\Country
     */
    public function getCountryModel()
    {
        return $this->countryFactory->create();
    }

    /**
     * @return void
     */
    public function processSyncFromMautic()
    {
        return;

    }

    /**
     * Get list objects
     *
     * @param string $filter
     * @param int $start
     * @param int $limit
     * @param string $orderBy
     * @param string $orderByDir
     * @return array|mixed|string|bool|null
     */
    public function getList($filter = "", $start = 0, $limit = 10, $orderBy = "", $orderByDir = "DESC")
    {
        $response = $this->getCurrentMauticApi()->getList($filter, $start, $limit, $orderBy, $orderByDir);
        return $response;
    }

    /**
     * @return bool
     */
    public function export()
    {
        return;

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
        if ($id) {
            $mauticApi = $this->getCurrentMauticApi();
            return $mauticApi->delete($id);
        }
        return false;
    }

    /**
     * get all companies
     *
     * @return array|bool|null
     */
    public function getCompanies()
    {
        if (!$this->_companies) {
            $response = $this->_getMauticApi("companies")->getList();
            if ($response && isset($response["companies"])) {
                $this->_companies = [];
                foreach ($response["companies"] as $_company) {
                    $this->_companies[] = [
                        "name" => $_company["fields"]["all"]["companyname"],
                        "value" => $_company["fields"]["all"]["companyname"]
                    ];
                }
            }
        }

        return $this->_companies;
    }

    /**
     * get all tags
     *
     * @return array|bool|null
     */
    public function getTags()
    {
        if (!$this->_tags) {
            $response = $this->_getMauticApi("tags")->getList();
            if ($response && isset($response["tags"])) {
                $this->_tags = [];
                foreach ($response["tags"] as $_tag) {
                    $this->_tags[] = [
                        "name" => $_tag["tag"],
                        "value" => $_tag["tag"]
                    ];
                }
            }
        }

        return $this->_tags;
    }

    /**
     * get all stages
     *
     * @return array|bool|null
     */
    public function getStages()
    {
        if (!$this->_stages) {
            $response = $this->_getMauticApi("stages")->getList();
            if ($response && isset($response["stages"])) {
                $this->_stages = [];
                foreach ($response["stages"] as $_stage) {
                    $this->_stages[] = [
                        "name" => $_stage["name"],
                        "value" => $_stage["id"]
                    ];
                }
            }
        }

        return $this->_stages;
    }

    /**
     * get all company fields
     *
     * @return array|bool|null
     */
    public function getCompanyFields()
    {
        if (!$this->_company_fields) {
            $response = $this->_getMauticApi("companyFields")->getList();
            if ($response && isset($response["fields"])) {
                $this->_company_fields = [];
                foreach ($response["fields"] as $field) {
                    $this->_company_fields[] = [
                        "label" => $field["label"],
                        "value" => $field["alias"]
                    ];
                }
            }
        }

        return $this->_company_fields;
    }

    /**
     * get all industries
     *
     * @return array|bool|null
     */
    public function getIndustries()
    {
        if (!$this->_industries) {
            $response = $this->_getMauticApi("companyFields")->getList();
            if ($response && isset($response["fields"])) {
                $this->_industries = [];
                foreach ($response["fields"] as $field) {
                    if ($field["alias"] === 'companyindustry') {
                        foreach ($field["properties"]["list"] as $_item_field) {
                            $this->_industries[] = [
                                "name" => $_item_field["label"],
                                "value" => $_item_field["value"]
                            ];
                        }
                    }
                }
            }
        }

        return $this->_industries;
    }

    /**
     * get all segments
     *
     * @return array|bool|null
     */
    public function getSegments()
    {
        if (!$this->_segments) {
            $response = $this->_getMauticApi("segments")->getList();
            if ($response && isset($response["lists"])) {
                $this->_segments = [];
                foreach ($response["lists"] as $_campaign) {
                    $this->_segments[] = [
                        "name" => $_campaign["name"],
                        "value" => $_campaign["id"]
                    ];
                }
            }
        }

        return $this->_segments;
    }

    /**
     * get all campaigns
     *
     * @return array|bool|null
     */
    public function getCampaigns()
    {
        if (!$this->_campaigns) {
            $response = $this->_getMauticApi("campaigns")->getList();
            if ($response && isset($response["campaigns"])) {
                $this->_campaigns = [];
                foreach ($response["campaigns"] as $_campaign) {
                    $this->_campaigns[] = [
                        "name" => $_campaign["name"],
                        "value" => $_campaign["id"]
                    ];
                }
            }
        }

        return $this->_campaigns;
    }

    /**
     * get all contact fields for mapping
     *
     * @return array|null
     */
    public function getContactFields()
    {
        if (!$this->_contact_fields) {
            $response = $this->_getMauticApi("contactFields")->getList();
            if ($response && isset($response["fields"])) {
                $this->_contact_fields = [];
                foreach ($response["fields"] as $field) {
                    $this->_contact_fields[] = [
                        "label" => $field["label"],
                        "value" => $field["alias"]
                    ];
                }
            }
        }

        return $this->_contact_fields;
    }

    /**
     * Retrieve mautic api for type: contacts, companies, campaigns, segments,..
     *
     * @return \Mautic\Api\Api
     */
    public function getCurrentMauticApi()
    {
        return $this->_getMauticApi($this->_api_type);
    }

    /**
     * Create Batch Data
     *
     * @param string $api_type
     * @param array $data
     * @return \Mautic\Api\Api
     */
    public function createBatchData($api_type, array $data)
    {
        $mauticApi = $this->_getMauticApi($api_type);
        return $mauticApi->createBatch($data);
    }

    /**
     * Edit Batch Data
     *
     * @param string $api_type
     * @param array $data
     * @return \Mautic\Api\Api
     */
    public function editBatchData($api_type, array $data)
    {
        $mauticApi = $this->_getMauticApi($api_type);
        return $mauticApi->editBatch($data);
    }

    /**
     * Send email to contact id
     *
     * @param string|int $emailId
     * @param string|int $contactId
     * @param array $parameters
     * @return \Mautic\Api\Api
     */
    public function sendEmailToContact($emailId, $contactId, array $parameters = [])
    {
        $mauticApi = $this->_getMauticApi("emails");
        return $mauticApi->sendToContact($emailId, $contactId, $parameters);
    }

    /**
     * Create Batch Data
     *
     * @param string $api_type
     * @param array $data
     * @return \Mautic\Api\Api
     */
    public function deleteBatch($api_type, array $ids)
    {
        $mauticApi = $this->_getMauticApi($api_type);
        return $mauticApi->deleteBatch($ids);
    }

    /**
     * Mapping mautic data to contacts data
     * @param array
     * @return array
     */
    public function mappingMauticData(array $data = [])
    {
        return $data;
    }

    /**
     * Mapping contact data to mautic data
     * @param array
     * @return array
     */
    public function mappingContactData(array $data = [])
    {
        return $data;
    }

    /**
     * get request data
     * @param array|null $data
     * @param mixed|array|object|null $customer
     * @return array
     */
    public function getRequestData(array $data = [], $customer = null)
    {
        return $data;
    }

    /**
     * Retrieve mautic api for type: contacts, companies, campaigns, segments,..
     *
     * @param string $api_type
     * @return \Mautic\Api\Api
     */
    protected function _getMauticApi($api_type = "contacts")
    {
        if (!isset($this->_contactApi[$api_type])) {
            $this->_contactApi[$api_type] = $this->mauticModel->getApi($api_type);
        }
        return $this->_contactApi[$api_type];
    }
}
