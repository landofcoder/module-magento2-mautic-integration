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
     * @return void|string|bool
     */
    public function execute() {
        sleep(20);
        $secretKey = $this->getRequest()->getParam("secret");
        $configSecretKey = $this->getHelperData()->getConfig("general/webhook_secret");
        if ($secretKey && $secretKey == $configSecretKey) {
            $rawData = $this->getMauticRequest();
            //TODO: write logic code at here
            //Action confirm subscription, unsubscription
            $secret = $this->getHelperData()->getConfig("general/webhook_mautic_secret");
            $secret = $secret ? $secret : "8b70a6c58459758787ad17af02ea9548a61814cf77ff9fbaf6e759e5292ec9b1";//mautic secret key
            // optional signature verification
            $headers = getallheaders();
            $receivedSignature = $headers['Webhook-Signature'];
            $rawData = !is_string($rawData) ? json_encode($rawData): $rawData;
            $computedSignature = base64_encode(hash_hmac('sha256', $rawData, $secret, true));
            $flag = false;
            if ($receivedSignature === $computedSignature) {
                $this->logger->info('Webhook authenticity verification OK');
                $flag = true;
            } else {
                $this->logger->info('Webhook not authentic!');
            }
            $this->logger->info($rawData);
            if ($flag) {
                // @todo Process the $requestData as needed
                $requestData = json_decode($rawData);
                // Process update subscriber status
                $this->logger->info("Mautic Webhook Processed successfully.");
                echo __("OK");
            } else {
                echo __("Webhook not authentic!");
            }
            return;
        }
        else {
            $this->logger->info("Mautic Webhook: Check secret key of webhook url is not match with value in config.");
            return;
        }
    }

     /**
     * Get the request JSON object and log the request
     *
     * @return object|string
     */
    protected function getMauticRequest()
    {
        $rawData = @file_get_contents("php://input");
        return $rawData;
    }
}
