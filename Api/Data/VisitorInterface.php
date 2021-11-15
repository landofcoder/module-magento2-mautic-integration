<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Api\Data;

interface VisitorInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const EMAIL = 'email';
    const FIRSTNAME = 'firstname';
    const COMPANY = 'company';
    const COMPANYEMAIL = 'companyemail';
    const UTM_CAMPAIGN = 'utm_campaign';
    const UTM_MEDIUM = 'utm_medium';
    const TAGS = 'tags';

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Lof\Mautic\Api\Data\VisitorInterface
     */
    public function setEmail($email);

    /**
     * Get firstname
     * @return string|null
     */
    public function getFirstname();

    /**
     * Set firstname
     * @param string $firstname
     * @return \Lof\Mautic\Api\Data\VisitorInterface
     */
    public function setFirstname($firstname);

    /**
     * Get company
     * @return string|null
     */
    public function getCompany();

    /**
     * Set company
     * @param string $company
     * @return \Lof\Mautic\Api\Data\VisitorInterface
     */
    public function setCompany($company);

    /**
     * Get companyemail
     * @return string|null
     */
    public function getCompanyemail();

    /**
     * Set companyemail
     * @param string $companyemail
     * @return \Lof\Mautic\Api\Data\VisitorInterface
     */
    public function setCompanyemail($companyemail);

    /**
     * Get utm_campaign
     * @return string|null
     */
    public function getUtmCampaign();

    /**
     * Set utm_campaign
     * @param string $utm_campaign
     * @return \Lof\Mautic\Api\Data\VisitorInterface
     */
    public function setUtmCampaign($utm_campaign);

    /**
     * Get utm_medium
     * @return string|null
     */
    public function getUtmMedium();

    /**
     * Set utm_medium
     * @param string $utm_medium
     * @return \Lof\Mautic\Api\Data\VisitorInterface
     */
    public function setUtmMedium($utm_medium);

    /**
     * Get tags
     * @return string|null
     */
    public function getTags();

    /**
     * Set tags
     * @param string $tags
     * @return \Lof\Mautic\Api\Data\VisitorInterface
     */
    public function setTags($tags);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\Mautic\Api\Data\VisitorExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\Mautic\Api\Data\VisitorExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\Mautic\Api\Data\VisitorExtensionInterface $extensionAttributes
    );
}
