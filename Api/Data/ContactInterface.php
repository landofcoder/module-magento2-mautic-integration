<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Api\Data;

interface ContactInterface
{
    const MAUTIC_CONTACT_ID = 'mautic_contact_id';
    const STAGE = 'stage';
    const TWITTER = 'twitter';
    const FACEBOOK = 'facebook';
    const HASPURCHASED = 'haspurchased';
    const SKYPE = 'skype';
    const B2B_OR_B2C = 'b2b_or_b2c';
    const INSTAGRAM = 'instagram';
    const LINKEDIN = 'linkedin';
    const WEBSITE = 'website';
    const CONTACT_ID = 'contact_id';
    const CRM_ID = 'crm_id';
    const PROSPECT_OR_CUSTOMER = 'prospect_or_customer';
    const CUSTOMER_ID = 'customer_id';
    const FOURSQUARE = 'foursquare';
    const TAGS = 'tags';

    /**
     * Get contact_id
     * @return int|null
     */
    public function getContactId();

    /**
     * Set contact_id
     * @param int $contactId
     * @return \Api\Data\ContactInterface
     */
    public function setContactId($contactId);

    /**
     * Get mautic_contact_id
     * @return string|null
     */
    public function getMauticContactId();

    /**
     * Set mautic_contact_id
     * @param string $mautic_contact_id
     * @return \Api\Data\ContactInterface
     */
    public function setMauticContactId($mautic_contact_id);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Api\Data\ContactInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get facebook
     * @return string|null
     */
    public function getFacebook();

    /**
     * Set facebook
     * @param string $facebook
     * @return \Api\Data\ContactInterface
     */
    public function setFacebook($facebook);

    /**
     * Get foursquare
     * @return string|null
     */
    public function getFoursquare();

    /**
     * Set foursquare
     * @param string $foursquare
     * @return \Api\Data\ContactInterface
     */
    public function setFoursquare($foursquare);

    /**
     * Get instagram
     * @return string|null
     */
    public function getInstagram();

    /**
     * Set instagram
     * @param string $instagram
     * @return \Api\Data\ContactInterface
     */
    public function setInstagram($instagram);

    /**
     * Get linkedin
     * @return string|null
     */
    public function getLinkedin();

    /**
     * Set linkedin
     * @param string $linkedin
     * @return \Api\Data\ContactInterface
     */
    public function setLinkedin($linkedin);

    /**
     * Get skype
     * @return string|null
     */
    public function getSkype();

    /**
     * Set skype
     * @param string $skype
     * @return \Api\Data\ContactInterface
     */
    public function setSkype($skype);

    /**
     * Get twitter
     * @return string|null
     */
    public function getTwitter();

    /**
     * Set twitter
     * @param string $twitter
     * @return \Api\Data\ContactInterface
     */
    public function setTwitter($twitter);

    /**
     * Get haspurchased
     * @return string|null
     */
    public function getHaspurchased();

    /**
     * Set haspurchased
     * @param string $haspurchased
     * @return \Api\Data\ContactInterface
     */
    public function setHaspurchased($haspurchased);

    /**
     * Get prospect_or_customer
     * @return string|null
     */
    public function getProspectOrCustomer();

    /**
     * Set prospect_or_customer
     * @param string $prospectOrCustomer
     * @return \Api\Data\ContactInterface
     */
    public function setProspectOrCustomer($prospectOrCustomer);

    /**
     * Get stage
     * @return string|null
     */
    public function getStage();

    /**
     * Set stage
     * @param string $stage
     * @return \Api\Data\ContactInterface
     */
    public function setStage($stage);

    /**
     * Get tags
     * @return string|null
     */
    public function getTags();

    /**
     * Set tags
     * @param string $tags
     * @return \Api\Data\ContactInterface
     */
    public function setTags($tags);

    /**
     * Get website
     * @return string|null
     */
    public function getWebsite();

    /**
     * Set website
     * @param string $website
     * @return \Api\Data\ContactInterface
     */
    public function setWebsite($website);

    /**
     * Get b2b_or_b2c
     * @return string|null
     */
    public function getB2bOrB2c();

    /**
     * Set b2b_or_b2c
     * @param string $b2bOrB2c
     * @return \Api\Data\ContactInterface
     */
    public function setB2bOrB2c($b2bOrB2c);

    /**
     * Get crm_id
     * @return string|null
     */
    public function getCrmId();

    /**
     * Set crm_id
     * @param string $crmId
     * @return \Api\Data\ContactInterface
     */
    public function setCrmId($crmId);
}

