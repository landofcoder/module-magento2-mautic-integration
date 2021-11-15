<?php
/**
 * Copyright © landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Model\Data;

use Lof\Mautic\Api\Data\VisitorInterface;

class Visitor extends \Magento\Framework\Api\AbstractExtensibleObject  implements VisitorInterface
{

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstname()
    {
        return $this->_get(self::FIRSTNAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstname($firstname)
    {
        return $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompany()
    {
        return $this->_get(self::COMPANY);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompany($company)
    {
        return $this->setData(self::COMPANY, $company);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompanyemail()
    {
        return $this->_get(self::COMPANYEMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompanyemail($companyemail)
    {
        return $this->setData(self::COMPANYEMAIL, $companyemail);
    }

    /**
     * {@inheritdoc}
     */
    public function getUtmCampaign()
    {
        return $this->_get(self::UTM_CAMPAIGN);
    }

    /**
     * {@inheritdoc}
     */
    public function setUtmCampaign($utm_campaign)
    {
        return $this->setData(self::UTM_CAMPAIGN, $utm_campaign);
    }

    /**
     * {@inheritdoc}
     */
    public function getUtmMedium()
    {
        return $this->_get(self::UTM_MEDIUM);
    }

    /**
     * {@inheritdoc}
     */
    public function setUtmMedium($utm_medium)
    {
        return $this->setData(self::UTM_MEDIUM, $utm_medium);
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->_get(self::TAGS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTags($tags)
    {
        return $this->setData(self::TAGS, $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Lof\Mautic\Api\Data\VisitorExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
