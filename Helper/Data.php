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

class Data extends AbstractHelper
{
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
    const CONTACT_INTEGRATION_STATUS = 'contact/enable';

    protected $_storeManager;
    protected $_directoryList;
    protected $encryptor;
    protected $serializer;
    protected $configWriter;
    protected $_cacheTypeList;
    protected $_cacheFrontendPool;
    protected $_backendHelperl;

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
        \Magento\Backend\Helper\Data $backendHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_directoryList  = $directoryList;
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
        $this->configWriter = $configWriter;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_backendHelper = $backendHelper;

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
        return $this->getConfigData('lofmautic/'.$key, $store);
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
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->getConfig("general/enabled");
    }

    /**
     * Retrieve mautic url
     *
     * @return string
     */
    public function getMauticUrl()
    {
        return $this->getConfig(self::MAUTIC_URL_XML_PATH);
    }

    /**
     * Retrieve Client key
     *
     * @return string
     */
    public function getClientKey()
    {
        return $this->getConfig(self::CLIENT_ID_XML_PATH);
    }

    /**
     * Retrieve client secret
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->getConfig(self::CLIENT_SECRET_URL_XML_PATH);
    }

    /**
     * Retrieve Oauth version
     *
     * @return string
     */
    public function getAuthType()
    {
        return $this->getConfig(self::OAUTH_TYPE_XML_PATH);
    }

    /**
     * Retrieve mautic login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->getConfig(self::BASE_AUTH_LOGIC);
    }

    /**
     * Retrieve base auth password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->decrypt($this->getConfig(self::BASE_AUTH_PASSWORD));
    }

    /**
     * Retrieve status of customer integration
     *
     * @return bool
     */
    public function isCustomerIntegrationEnabled()
    {
        return (bool)$this->getConfig(self::CONTACT_INTEGRATION_STATUS);
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
            $this->configWriter->delete(self::CLIENT_ACCESS_TOKEN_XML_PATH.'_'.$oauth_version);
        }
        else {
            $this->configWriter->save(self::CLIENT_ACCESS_TOKEN_XML_PATH.'_'.$oauth_version, $tokenJson);
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
        if (class_exists(System::class)) {
            $this->objectManager->get(System::class)->clean();
        } else {
            $this->objectManager->get(Config::class)
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
        if (!preg_match('/^[A-Za-z0-9_]+$/', $key))
            $key = $this->encryptor->decrypt($key);

        return trim($key);
    }

    /**
     * Retrieve callback url
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->_backendHelper->getUrl('mautic/authorize');
    }

}
