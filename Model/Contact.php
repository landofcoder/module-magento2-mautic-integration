<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Model;

use Lof\Mautic\Api\Data\ContactInterface;

class Contact extends \Magento\Framework\Model\AbstractModel implements ContactInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Lof\Mautic\Model\ResourceModel\Contact::class);
    }

    /**
     * Get contact_id
     * @return int|null
     */
    public function getContactId()
    {
        return $this->getData(self::CONTACT_ID);
    }

    /**
     * Set contact_id
     * @param int $contactId
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setContactId($contactId)
    {
        return $this->setData(self::CONTACT_ID, $contactId);
    }

    /**
     * Get mautic_contact_id
     * @return string|null
     */
    public function getMauticContactId()
    {
        return $this->getData(self::MAUTIC_CONTACT_ID);
    }

    /**
     * Set mautic_contact_id
     * @param string $mautic_contact_id
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setMauticContactId($mautic_contact_id)
    {
        return $this->setData(self::MAUTIC_CONTACT_ID, $contactId);
    }

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get facebook
     * @return string|null
     */
    public function getFacebook()
    {
        return $this->getData(self::FACEBOOK);
    }

    /**
     * Set facebook
     * @param string $facebook
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setFacebook($facebook)
    {
        return $this->setData(self::FACEBOOK, $facebook);
    }

    /**
     * Get foursquare
     * @return string|null
     */
    public function getFoursquare()
    {
        return $this->getData(self::FOURSQUARE);
    }

    /**
     * Set foursquare
     * @param string $foursquare
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setFoursquare($foursquare)
    {
        return $this->setData(self::FOURSQUARE, $foursquare);
    }

    /**
     * Get instagram
     * @return string|null
     */
    public function getInstagram()
    {
        return $this->getData(self::INSTAGRAM);
    }

    /**
     * Set instagram
     * @param string $instagram
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setInstagram($instagram)
    {
        return $this->setData(self::INSTAGRAM, $instagram);
    }

    /**
     * Get linkedin
     * @return string|null
     */
    public function getLinkedin()
    {
        return $this->getData(self::LINKEDIN);
    }

    /**
     * Set linkedin
     * @param string $linkedin
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setLinkedin($linkedin)
    {
        return $this->setData(self::LINKEDIN, $linkedin);
    }

    /**
     * Get skype
     * @return string|null
     */
    public function getSkype()
    {
        return $this->getData(self::SKYPE);
    }

    /**
     * Set skype
     * @param string $skype
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setSkype($skype)
    {
        return $this->setData(self::SKYPE, $skype);
    }

    /**
     * Get twitter
     * @return string|null
     */
    public function getTwitter()
    {
        return $this->getData(self::TWITTER);
    }

    /**
     * Set twitter
     * @param string $twitter
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setTwitter($twitter)
    {
        return $this->setData(self::TWITTER, $twitter);
    }

    /**
     * Get haspurchased
     * @return string|null
     */
    public function getHaspurchased()
    {
        return $this->getData(self::HASPURCHASED);
    }

    /**
     * Set haspurchased
     * @param string $haspurchased
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setHaspurchased($haspurchased)
    {
        return $this->setData(self::HASPURCHASED, $haspurchased);
    }

    /**
     * Get prospect_or_customer
     * @return string|null
     */
    public function getProspectOrCustomer()
    {
        return $this->getData(self::PROSPECT_OR_CUSTOMER);
    }

    /**
     * Set prospect_or_customer
     * @param string $prospectOrCustomer
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setProspectOrCustomer($prospectOrCustomer)
    {
        return $this->setData(self::PROSPECT_OR_CUSTOMER, $prospectOrCustomer);
    }

    /**
     * Get stage
     * @return string|null
     */
    public function getStage()
    {
        return $this->getData(self::STAGE);
    }

    /**
     * Set stage
     * @param string $stage
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setStage($stage)
    {
        return $this->setData(self::STAGE, $stage);
    }

    /**
     * Get tags
     * @return string|null
     */
    public function getTags()
    {
        return $this->getData(self::TAGS);
    }

    /**
     * Set tags
     * @param string $tags
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setTags($tags)
    {
        return $this->setData(self::TAGS, $tags);
    }

    /**
     * Get website
     * @return string|null
     */
    public function getWebsite()
    {
        return $this->getData(self::WEBSITE);
    }

    /**
     * Set website
     * @param string $website
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setWebsite($website)
    {
        return $this->setData(self::WEBSITE, $website);
    }

    /**
     * Get b2b_or_b2c
     * @return string|null
     */
    public function getB2bOrB2c()
    {
        return $this->getData(self::B2B_OR_B2C);
    }

    /**
     * Set b2b_or_b2c
     * @param string $b2bOrB2c
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setB2bOrB2c($b2bOrB2c)
    {
        return $this->setData(self::B2B_OR_B2C, $b2bOrB2c);
    }

    /**
     * Get crm_id
     * @return string|null
     */
    public function getCrmId()
    {
        return $this->getData(self::CRM_ID);
    }

    /**
     * Set crm_id
     * @param string $crmId
     * @return \Lof\Mautic\Api\Data\ContactInterface
     */
    public function setCrmId($crmId)
    {
        return $this->setData(self::CRM_ID, $crmId);
    }
}
