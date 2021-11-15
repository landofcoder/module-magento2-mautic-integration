<?php

namespace Lof\Mautic\Model\Mautic;

use Mautic\Auth\ApiAuth;
use Mautic\MauticApi;
use Lof\Mautic\Model\Config\Source\OauthVersion;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Company extends AbstractApi
{
    /**
     * @var string
     */
    protected $_api_type = "companies";

    /**
     * @var \Lof\Mautic\Model\CompanyFactory
     */
    protected $companyFactory;


     /**
     * Initialize resource model
     *
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Lof\Mautic\Model\Mautic $mauticModel
     * @param \Lof\Mautic\Model\CompanyFactory $companyFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Lof\Mautic\Model\Mautic $mauticModel,
        \Lof\Mautic\Model\CompanyFactory $companyFactory
    ) {
        parent::__construct($context, $registry, $customerFactory, $countryFactory, $mauticModel );
        $this->companyFactory = $companyFactory;
    }

    /**
     * Export companies data to Mautic
     *
     * @return bool
     */
    public function export()
    {
        $companies = $this->companyFactory->create()->getCollection()
            ->addAttributeToSelect('*');

        foreach ($companies as $company) {
            $this->exportCompany($company);
        }

        return true;
    }

    /**
     * @return void
     */
    public function processSyncFromMautic()
    {
        $listContacts = $this->getList();
        if ($listContacts && isset($listContacts['companies'])) {
            foreach ($listContacts['companies'] as $company) {
                $this->createCompany($company);
            }
        }
        return;

    }

    /**
     * Export company
     *
     * @param Object|array
     * @param array|null $customData
     * @return bool
     */
    public function exportCompany($company, $customData = [])
    {
        $data = $company->getData();
        //TODO: get company custom fields in company and mapping data before push to mautic
        /**
         * customFieldValues [ 'fieldId' => fieldID, 'fieldValue' => fieldValue] convert to [fieldID => fieldValue]
         */

        if ($customData) {
            $data = array_merge($data, $customData);
        }

        if (isset($data['mautic_company_id']) && (int)$data['mautic_company_id']) {
            $mautic_company_id = (int)$data['mautic_company_id'];
            unset($data['mautic_company_id']);
            $response = $this->getCurrentMauticApi()->edit($mautic_company_id, $data);
        } else {
            $response = $this->getCurrentMauticApi()->create($data);
        }
        if (isset($response['errors']) && count($response['errors'])) {
            $this->mauticModel
                ->executeErrorResponse($response);

            return false;
        }

        return true;
    }

    /**
     * create company on magento table
     * @param array|mixed $company
     * @return Object|mixed|boolean
     */
    public function createCompany($company = [])
    {
        if (isset($company['fields']) && isset($company['fields']['all']) && $company['id']) {
            $companyFields = $company['fields']['all'];
            $companyItem = $this->companyFactory->create()->getCollection()
                                ->addFieldToFilter("mautic_company_id", $company['id'])
                                ->getFirstItem();
            $companyId = 0;
            if($companyItem) {
                $model = $this->companyFactory->create()->load($companyItem->getCompanyId());
                $companyId = $model->getId();
            } else {
                $model = $this->companyFactory->create();
            }

            $data = [
                "mautic_company_id" => $company['id'],
                "companyname" => isset($companyFields['companyname']) ? $companyFields['companyname'] :'',
                "companyaddress1" => isset($companyFields['companyaddress1']) ? $companyFields['companyaddress1'] :'',
                "companyaddress2" => isset($companyFields['companyaddress2']) ? $companyFields['companyaddress2'] :'',
                "companycity" => isset($companyFields['companycity']) ? $companyFields['companycity'] :'',
                "companystate" => isset($companyFields['companystate']) ? $companyFields['companystate'] :'',
                "companycountry" => isset($companyFields['companycountry']) ? $companyFields['companycountry'] :'',
                "companyzipcode" => isset($companyFields['companyzipcode']) ? $companyFields['companyzipcode'] :'',
                "companyemail" => isset($companyFields['companyemail']) ? $companyFields['companyemail'] :'',
                "companyindustry" => isset($companyFields['companyindustry']) ? $companyFields['companyindustry'] :'',
                "companynumber_of_employees" => isset($companyFields['companynumber_of_employees']) ? $companyFields['companynumber_of_employees'] :'',
                "companyphone" => isset($companyFields['companyphone']) ? $companyFields['companyphone'] :'',
                "companywebsite" => isset($companyFields['companywebsite']) ? $companyFields['companywebsite'] :'',
                "companyannual_revenue" => isset($companyFields['companyannual_revenue']) ? $companyFields['companyannual_revenue'] :'',
                "companydescription" => isset($companyFields['companydescription']) ? $companyFields['companydescription'] :''
            ];
            $data['company_id'] = $companyId;

            //TODO: get company custom fields then mapping data at here

            $model->setData($data);
            try {
                $model->save();
            } catch (\Exception $e) {
                //log exception at here
            }
            return $model;
        }
        return false;
    }
}
