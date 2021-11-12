<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Api\Data;

interface CompanyInterface
{

    const COMPANYWEBSITE = 'companywebsite';
    const COMPANYANNUAL_REVENUE = 'companyannual_revenue';
    const MAUTIC_COMPANY_ID = 'mautic_company_id';
    const COMPANYADDRESS1 = 'companyaddress1';
    const COMPANYEMAIL = 'companyemail';
    const COMPANYADDRESS2 = 'companyaddress2';
    const COMPANYNUMBER_OF_EMPLOYEES = 'companynumber_of_employees';
    const COMPANYDESCRIPTION = 'companydescription';
    const COMPANYINDUSTRY = 'companyindustry';
    const COMPANYPHONE = 'companyphone';
    const COMPANYCITY = 'companycity';
    const COMPANYZIPCODE = 'companyzipcode';
    const COMPANYCOUNTRY = 'companycountry';
    const COMPANYNAME = 'companyname';
    const COMPANYSTATE = 'companystate';
    const COMPANY_ID = 'company_id';

    /**
     * Get company_id
     * @return string|null
     */
    public function getCompanyId();

    /**
     * Set company_id
     * @param string $companyId
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanyId($companyId);

    /**
     * Get companyname
     * @return string|null
     */
    public function getCompanyname();

    /**
     * Set companyname
     * @param string $companyname
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanyname($companyname);

    /**
     * Get mautic_company_id
     * @return string|null
     */
    public function getMauticCompanyId();

    /**
     * Set mautic_company_id
     * @param string $mauticCompanyId
     * @return \Api\Data\CompanyInterface
     */
    public function setMauticCompanyId($mauticCompanyId);

    /**
     * Get companyaddress1
     * @return string|null
     */
    public function getCompanyaddress1();

    /**
     * Set companyaddress1
     * @param string $companyaddress1
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanyaddress1($companyaddress1);

    /**
     * Get companyaddress2
     * @return string|null
     */
    public function getCompanyaddress2();

    /**
     * Set companyaddress2
     * @param string $companyaddress2
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanyaddress2($companyaddress2);

    /**
     * Get companycity
     * @return string|null
     */
    public function getCompanycity();

    /**
     * Set companycity
     * @param string $companycity
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanycity($companycity);

    /**
     * Get companystate
     * @return string|null
     */
    public function getCompanystate();

    /**
     * Set companystate
     * @param string $companystate
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanystate($companystate);

    /**
     * Get companycountry
     * @return string|null
     */
    public function getCompanycountry();

    /**
     * Set companycountry
     * @param string $companycountry
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanycountry($companycountry);

    /**
     * Get companyzipcode
     * @return string|null
     */
    public function getCompanyzipcode();

    /**
     * Set companyzipcode
     * @param string $companyzipcode
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanyzipcode($companyzipcode);

    /**
     * Get companyemail
     * @return string|null
     */
    public function getCompanyemail();

    /**
     * Set companyemail
     * @param string $companyemail
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanyemail($companyemail);

    /**
     * Get companyindustry
     * @return string|null
     */
    public function getCompanyindustry();

    /**
     * Set companyindustry
     * @param string $companyindustry
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanyindustry($companyindustry);

    /**
     * Get companynumber_of_employees
     * @return string|null
     */
    public function getCompanynumberOfEmployees();

    /**
     * Set companynumber_of_employees
     * @param string $companynumberOfEmployees
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanynumberOfEmployees($companynumberOfEmployees);

    /**
     * Get companyphone
     * @return string|null
     */
    public function getCompanyphone();

    /**
     * Set companyphone
     * @param string $companyphone
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanyphone($companyphone);

    /**
     * Get companywebsite
     * @return string|null
     */
    public function getCompanywebsite();

    /**
     * Set companywebsite
     * @param string $companywebsite
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanywebsite($companywebsite);

    /**
     * Get companyannual_revenue
     * @return string|null
     */
    public function getCompanyannualRevenue();

    /**
     * Set companyannual_revenue
     * @param string $companyannualRevenue
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanyannualRevenue($companyannualRevenue);

    /**
     * Get companydescription
     * @return string|null
     */
    public function getCompanydescription();

    /**
     * Set companydescription
     * @param string $companydescription
     * @return \Api\Data\CompanyInterface
     */
    public function setCompanydescription($companydescription);
}

