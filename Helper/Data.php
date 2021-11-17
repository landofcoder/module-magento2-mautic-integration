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

class Data extends AbstractHelper
{
    /**
     * Path of base module settings
     */
    const MODULE_BASE_SETTING_XML_PATH = 'lofmautic';

    /**
     * Path to module status
     */
    const MODULE_STATUS_XML_PATH = 'general/enable';

    /**
     * Path to mautic url configuration
     */
    const MAUTIC_URL_XML_PATH = 'general/mautic_url';

    /**
     * Path to client id configuration
     */
    const CLIENT_ID_XML_PATH = 'general/client_id';

    /**
     * Path to oauth version
     */
    const OAUTH_TYPE_XML_PATH = 'general/oauth_version';

    /**
     * Path to client secret configuration
     */
    const CLIENT_SECRET_URL_XML_PATH = 'general/client_secret';

    /**
     * Path to access token
     */
    const CLIENT_ACCESS_TOKEN_XML_PATH = 'general/access_token_data';

    /**
     * Path to basic auth login configuration
     */
    const BASE_AUTH_LOGIC = 'general/mautic_login';

    /**
     * Path to basic auth password configuration
     */
    const BASE_AUTH_PASSWORD = 'general/mautic_password';

    /**
     * Contact integration status path
     */
    const CONTACT_INTEGRATION_STATUS = 'contact/enabled';

    /**
     * Newsletter is disabled
     */
    const NEWSLETTER_STATUS = 'newsletter/disable_magento_subscription';

    /**
     * Company integration status path
     */
    const COMPANY_INTEGRATION_STATUS = 'company/enabled';

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
        return (bool)$this->getConfig(self::NEWSLETTER_STATUS, $store);
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
        return $this->getConfig(self::MAUTIC_URL_XML_PATH, $store);
    }

    /**
     * Retrieve Client key
     * @param mixed|Object|int|null $store
     * @return string
     */
    public function getClientKey($store = null)
    {
        return $this->getConfig(self::CLIENT_ID_XML_PATH, $store);
    }

    /**
     * Retrieve client secret
     * @param mixed|Object|int|null $store
     * @return string
     */
    public function getClientSecret($store = null)
    {
        return $this->decrypt($this->getConfig(self::CLIENT_SECRET_URL_XML_PATH, $store));
    }

    /**
     * Retrieve Oauth version
     * @param mixed|Object|int|null $store
     * @return string
     */
    public function getAuthType($store = null)
    {
        return $this->getConfig(self::OAUTH_TYPE_XML_PATH, $store);
    }

    /**
     * Retrieve mautic login
     * @param mixed|Object|int|null $store
     * @return string
     */
    public function getLogin($store = null)
    {
        return $this->getConfig(self::BASE_AUTH_LOGIC, $store);
    }

    /**
     * Retrieve base auth password
     * @param mixed|Object|int|null $store
     * @return string
     */
    public function getPassword($store = null)
    {
        return $this->decrypt($this->getConfig(self::BASE_AUTH_PASSWORD, $store));
    }

    /**
     * Retrieve status of customer integration
     * @param mixed|Object|int|null $store
     * @return bool
     */
    public function isCustomerIntegrationEnabled($store = null)
    {
        return (bool)$this->getConfig(self::CONTACT_INTEGRATION_STATUS, $store);
    }


    /**
     * Retrieve status of company integration
     * @param mixed|Object|int|null $store
     * @return bool
     */
    public function isCompanyIntegrationEnabled($store = null)
    {
        return (bool)$this->getConfig(self::COMPANY_INTEGRATION_STATUS, $store);
    }

    /**
     * Retrieve access token data
     *
     * @param mixed|Object|int|null $store
     * @return bool|array
     */
    public function getStoredAccessTokenData($store = null)
    {
        if ($this->getConfig(self::OAUTH_TYPE_XML_PATH)) {
            $oauth_version = strtolower($this->getConfig(self::OAUTH_TYPE_XML_PATH));
            $token = $this->getConfig(self::CLIENT_ACCESS_TOKEN_XML_PATH.'_'.$oauth_version, $store);
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
        $oauth_version = $this->getConfig(self::OAUTH_TYPE_XML_PATH);
        $oauth_version = $oauth_version ? strtolower($oauth_version) : null;
        if (empty($accessTokenData)) {
            $this->configWriter->delete(self::MODULE_BASE_SETTING_XML_PATH."/".self::CLIENT_ACCESS_TOKEN_XML_PATH.'_'.$oauth_version);
        }
        else {
            $this->configWriter->save(self::MODULE_BASE_SETTING_XML_PATH."/".self::CLIENT_ACCESS_TOKEN_XML_PATH.'_'.$oauth_version, $tokenJson);
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
    public function getStoreUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get current store name
     *
     * @return string
     */
    public function getCurrentStoreNmae()
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * Get default store tags
     *
     * @return array
     */
    public function getDefaultTags()
    {
        $tags = [];
        $tags[] = "magento2";
        $tags[] = $this->getCurrentStoreNmae();
        return $tags;
    }

    /**
     * Get newsletter email id
     *
     * @return int|string
     */
    public function getNewsletterEmailId()
    {
        return 0;
    }

}
