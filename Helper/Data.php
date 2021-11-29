<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Mautic
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
declare(strict_types=1);

namespace Lof\Mautic\Helper;

use Magento\Framework\App\Cache\Type\Config;
use Magento\Config\App\Config\Type\System;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
    /**
     * Path of abandoned firstdate
     */
    const MODULE_FIRST_DATE = 'abandoned/firstdate';

    /**
     * Path of abandoned diffdate
     */
    const MODULE_DIFF_DATE = 'abandoned/diffdate';

    /**
     * Path of abandoned customer groups
     */
    const MODULE_ABANDONED_CUSTOMER_GROUP = 'abandoned/customer_group';

    /**
     * Path of abandoned page
     */
    const MODULE_ABANDONEDCART_PAGE = 'abandoned/page';

    /**
     * Path of abandoned token
     */
    const MODULE_ABANDONEDCART_TOKEN = 'abandoned/loadquote_token';

    /**
     * Path of base module settings
     */
    const MODULE_BASE_SETTING_XML_PATH = 'lofmautic';

    /**
     * Path to module status
     */
    const MODULE_STATUS_XML_PATH = 'general/enable';

    /**
     * Path to enable async api
     */
    const MODULE_ASYNC_API_PATH = 'sync_mautic/async_api';

    /**
     * Path to mautic url configuration
     */
    const MODULE_MAUTIC_URL_XML_PATH = 'general/mautic_url';

    /**
     * Path to client id configuration
     */
    const MODULE_CLIENT_ID_XML_PATH = 'general/client_id';

    /**
     * Path to oauth version
     */
    const MODULE_OAUTH_TYPE_XML_PATH = 'general/oauth_version';

    /**
     * Path to client secret configuration
     */
    const MODULE_CLIENT_SECRET_URL_XML_PATH = 'general/client_secret';

    /**
     * Path to access token
     */
    const MODULE_CLIENT_ACCESS_TOKEN_XML_PATH = 'general/access_token_data';

    /**
     * Path to basic auth login configuration
     */
    const MODULE_BASE_AUTH_LOGIC = 'general/mautic_login';

    /**
     * Path to basic auth password configuration
     */
    const MODULE_BASE_AUTH_PASSWORD = 'general/mautic_password';


    /**
     * Contact integration status path
     */
    const MODULE_CONTACT_INTEGRATION_STATUS = 'contact/enabled';

    /**
     * Newsletter is disabled
     */
    const MODULE_NEWSLETTER_STATUS = 'newsletter/disable_magento_subscription';

    /**
     * Mapping fieds
     */
    const MODULE_FIELDS_MAPPING_PATH = 'fields_mapping/fields';

    /**
     * Company integration status path
     */
    const MODULE_COMPANY_INTEGRATION_STATUS = 'company/enabled';

    /**
     * Base url path
     */
    const XML_PATH_BASE_URL = 'web/unsecure/base_url';

    /**
     * Default tags for abandoned cart
     */
    const ABANDONED_CART_TAGS = 'AbandonedCart';

    protected $_storeManager;
    protected $_directoryList;
    protected $encryptor;
    protected $serializer;
    protected $configWriter;
    protected $_cacheTypeList;
    protected $_cacheFrontendPool;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Backend\Helper\Data $backendHelper,
        CustomerFactory $customerFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_directoryList  = $directoryList;
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
        $this->configWriter = $configWriter;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_backendHelper = $backendHelper;
        $this->customerFactory = $customerFactory;

        parent::__construct($context);
    }

     /**
     * Return module config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
     public function getConfig($key, $store = null)
    {
        return $this->getConfigData(self::MODULE_BASE_SETTING_XML_PATH.'/'.$key, $store);
    }

    /**
     * Get config data
     *
     * @param string $path
     * @param mixed|Object|int|null $store
     *
     * @return mixed|string|array|int|bool|null
     */
    public function getConfigData($path, $store = null){
        $store = $this->_storeManager->getStore($store);
        $result =  $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    /**
     * Is module enabled
     *
     * @param mixed|Object|int|null $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return (bool)$this->getConfig("general/enabled", $store);
    }

    /**
     * Is module disabled newsletter send email
     *
     * @param mixed|Object|int|null $store
     * @return bool
     */
    public function isDisabledNewsletter($store = null)
    {
        return (bool)$this->getConfig(self::MODULE_NEWSLETTER_STATUS, $store);
    }

    /**
     * Is enable Async Mautic API
     *
     * @param mixed|Object|int|null $store
     * @return bool
     */
    public function isAyncApi($store = null)
    {
        return (bool)$this->getConfig(self::MODULE_ASYNC_API_PATH, $store);
    }

    /**
     * Get Mautic Base Url
     *
     * @param mixed|Object|int|null $store
     * @return string
     */
    public function getMauticBaseUrl($store = null)
    {
        return $this->getConfig("general/mautic_url", $store);
    }

    /**
     * Retrieve mautic url
     * @param mixed|Object|int|null $store
     * @return string
     */
    public function getMauticUrl($store = null)
    {
        return $this->getConfig(self::MODULE_MAUTIC_URL_XML_PATH, $store);
    }

    /**
     * Retrieve Client key
     * @param mixed|Object|int|null $store
     * @return string
     */
    public function getClientKey($store = null)
    {
        return $this->getConfig(self::MODULE_CLIENT_ID_XML_PATH, $store);
    }

    /**
     * Retrieve client secret
     * @param mixed|Object|int|null $store
     * @return string
     */
    public function getClientSecret($store = null)
    {
        return $this->decrypt($this->getConfig(self::MODULE_CLIENT_SECRET_URL_XML_PATH, $store));
    }

    /**
     * Retrieve Oauth version
     * @param mixed|Object|int|null $store
     * @return string
     */
    public function getAuthType($store = null)
    {
        return $this->getConfig(self::MODULE_OAUTH_TYPE_XML_PATH, $store);
    }

    /**
     * Retrieve mautic login
     * @param mixed|Object|int|null $store
     * @return string
     */
    public function getLogin($store = null)
    {
        return $this->getConfig(self::MODULE_BASE_AUTH_LOGIC, $store);
    }

    /**
     * Retrieve base auth password
     * @param mixed|Object|int|null $store
     * @return string
     */
    public function getPassword($store = null)
    {
        return $this->decrypt($this->getConfig(self::MODULE_BASE_AUTH_PASSWORD, $store));
    }

    /**
     * Retrieve status of customer integration
     * @param mixed|Object|int|null $store
     * @return bool
     */
    public function isCustomerIntegrationEnabled($store = null)
    {
        return (bool)$this->getConfig(self::MODULE_CONTACT_INTEGRATION_STATUS, $store);
    }


    /**
     * Retrieve status of company integration
     * @param mixed|Object|int|null $store
     * @return bool
     */
    public function isCompanyIntegrationEnabled($store = null)
    {
        return (bool)$this->getConfig(self::MODULE_COMPANY_INTEGRATION_STATUS, $store);
    }

    /**
     * Retrieve access token data
     *
     * @param mixed|Object|int|null $store
     * @return bool|array
     */
    public function getStoredAccessTokenData($store = null)
    {
        if ($this->getConfig(self::MODULE_OAUTH_TYPE_XML_PATH)) {
            $oauth_version = strtolower($this->getConfig(self::MODULE_OAUTH_TYPE_XML_PATH));
            $token = $this->getConfig(self::MODULE_CLIENT_ACCESS_TOKEN_XML_PATH.'_'.$oauth_version, $store);
            if ($token) {
                return $this->serializer->unserialize($token);
            }
        }

        return false;
    }

    /**
     * Save access token
     *
     * @param $accessTokenData array
     * @return bool
     */
    public function updateStoredAccessTokenData($accessTokenData = array())
    {
        $tokenJson = $this->serializer->serialize($accessTokenData);
        $oauth_version = $this->getConfig(self::MODULE_OAUTH_TYPE_XML_PATH);
        $oauth_version = $oauth_version ? strtolower($oauth_version) : null;
        if (empty($accessTokenData)) {
            $this->configWriter->delete(self::MODULE_BASE_SETTING_XML_PATH."/".self::MODULE_CLIENT_ACCESS_TOKEN_XML_PATH.'_'.$oauth_version);
        }
        else {
            $this->configWriter->save(self::MODULE_BASE_SETTING_XML_PATH."/".self::MODULE_CLIENT_ACCESS_TOKEN_XML_PATH.'_'.$oauth_version, $tokenJson);
        }

        $this->flushConfigCache();
        $this->flushCache();

        return $this;
    }

    /**
     * flush cache
     */
    protected function flushCache(){
        $types = array('config');
        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }
    }

    /**
     * Flush config cache
     */
    public function flushConfigCache()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (class_exists(System::class)) {
            $objectManager->get(System::class)->clean();
        } else {
            $objectManager->get(Config::class)
                ->clean(
                    \Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                    ['config_scopes']
                );
        }
    }

    /**
     * Decrypt hash code
     *
     * @param string $key
     * @return string
     */
    public function decrypt($key)
    {
        if ($key) {
            if (!preg_match('/^[A-Za-z0-9_]+$/', $key))
                $key = $this->encryptor->decrypt($key);
            $key = trim($key);
        }

        return $key;
    }

    /**
     * encode object
     *
     * @param mixed|Object|array
     * @return string
     */
    public function encodeData($object)
    {
        return $this->serializer->serialize($object);
    }

    /**
     * decode string
     *
     * @param string
     * @return mixed|array|Object
     */
    public function decodeData($string)
    {
        return $this->serializer->unserialize($string);
    }

    /**
     * Get customer id
     * @return Object|array|mixed
     */
    public function getCustomerById($customer_id)
    {
        return $this->customerFactory->create()->load($customer_id);
    }

    /**
     * Retrieve callback url
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        $authType = $this->getAuthType();
        $typeString = "";
        if ($authType == \Lof\Mautic\Model\Config\Source\OauthVersion::AUTH_OAUTH2) {
            $typeString = "/version/2";
        }
        return $this->_backendHelper->getUrl('mautic/configurable/authorize'.$typeString);
    }

    /**
     * Get store base url
     *
     * @return string
     */
    public function getStoreUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get current store name
     *
     * @return string
     */
    public function getCurrentStoreNmae(): string
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * Get default store tags
     *
     * @return array
     */
    public function getDefaultTags(): array
    {
        $tags = [];
        $tags[] = "magento2";
        $tags[] = $this->getCurrentStoreNmae();
        return $tags;
    }

    /**
     * get mautic tags by type
     *
     * @param string $type - get by type: new_customer, new_invoice, subscription, review
     * @param bool $add - is add or substract
     * @param mixed|null $store
     * @return array
     */
    public function getMauticTags($type = "new_customer", $add = true , $store = null)
    {
        $tags = [];
        $tmpTags = [];
        $value = "";
        switch ($type) {
            case "new_customer":
                $value = $this->getConfig("tags_points/new_customer_tags", $store);
                break;
            case "new_invoice":
                $value = $this->getConfig("tags_points/new_invoice_tags", $store);
                break;
            case "subscription":
                $value = $this->getConfig("tags_points/new_subscription_tags", $store);
                break;
            case "review":
                $value = $this->getConfig("tags_points/new_review_tags", $store);
                break;
            default:
                break;
        }
        $tmpTags = $value ? explode(",", $value) : [];
        if ($tmpTags) {
            foreach ($tmpTags as $tag) {
                if ($tags) {
                    $tags[] = $add ? $tag : "-".$tag;
                }
            }
        }
        return $tags;
    }

    /**
     * get mautic point by type
     *
     * @param string $type - get by type: new_customer, new_invoice, subscription, review
     * @param bool $add - is add or substract
     * @param mixed|null $store
     * @return int
     */
    public function getMauticPoint($type = "new_customer", $add = true , $store = null)
    {
        $value = 0;
        switch ($type) {
            case "new_customer":
                $value = (int)$this->getConfig("tags_points/new_customer_points", $store);
                break;
            case "new_invoice":
                $value = (int)$this->getConfig("tags_points/new_invoice_points", $store);
                break;
            case "subscription":
                $value = (int)$this->getConfig("tags_points/new_subscription_points", $store);
                break;
            case "review":
                $value = (int)$this->getConfig("tags_points/new_review_points", $store);
                break;
            default:
                break;
        }

        return $value > 0 && !$add ? -$value : $value;
    }

    /**
     * Get Mapping Fields
     *
     * @return array
     */
    public function getMappingFields(): array
    {
        $value = $this->getConfig(self::MODULE_FIELDS_MAPPING_PATH);
        return !empty($value) ? $this->serializer->unserialize($value) : [];
    }

    /**
     * Get website Base url
     *
     * @param int $websiteId
     * @return string
     */
    public function getWebsiteBaseUrl(int $websiteId = 0): string
    {
        $scopeDefault = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        $scopeWebsite = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        $baseUrlDefault = $this->scopeConfig->getValue(self::XML_PATH_BASE_URL, $scopeDefault);
        $baseUrl = "";
        if ($websiteId) {
            $baseUrl = $this->scopeConfig->getValue(self::XML_PATH_BASE_URL, $scopeWebsite, $websiteId);
        }
        return $baseUrl ? $baseUrl : $baseUrlDefault;
    }

}
