<?php

namespace Lof\Mautic\Controller\Webhook;

use Magento\Framework\DataObject;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;

class Index extends \Lof\Mautic\Controller\MauticAbstract {

    protected $logger;

    protected $storeManagement;

    protected $objectManagement;
    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\Mautic\Helper\Data $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagement
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\Mautic\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManagement,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        parent::__construct(
            $context,
            $customerSession,
            $helperData,
            $resultJsonFactory
        );
        $this->logger           = $logger;
        $this->storeManagement  = $storeManagement;
        $this->objectManagement = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Execute webhook in case of network failure
     *
     * @return void
     */
    public function execute() {
        sleep(20);
        $params = $this->getRequest()->getParams();
        $secretKey = $this->getRequest()->getParam("secret");
        $configCecretKey = $this->getHelperData()->getConfig("general/webhook_secret");

        if ($secretKey && $secretKey == $configCecretKey) {

            //TODO: write logic code at here

            $this->logger->info("Mautic Webhook Processed successfully.");
            return;
        }
        else {
            $this->logger->info("Mautic Webhook: Check secret key of webhook url is not match with value in config.");
            return;
        }
    }
}
