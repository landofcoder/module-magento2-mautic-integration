<?php

namespace Lof\Mautic\Controller\Webhook;

use Magento\Framework\DataObject;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Newsletter\Model\Subscriber;

class Index extends \Lof\Mautic\Controller\MauticAbstract {

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManagement;

    /**
     * @var \Magento\Framework\App\ObjectManager|mixed|object
     */
    protected $objectManagement;

    /**
     * Subscriber factory
     *
     * @var SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\Mautic\Helper\Data $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagement
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\Mautic\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManagement,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        SubscriberFactory $subscriberFactory
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
        $this->_subscriberFactory = $subscriberFactory;
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
            //Action confirm subscription, unsubscription
            $secret = $this->getHelperData()->getConfig("general/webhook_mautic_secret");
            // optional signature verification
            $headers = getallheaders();
            $receivedSignature = $headers['Webhook-Signature'];
            $rawData = !is_string($rawData) ? json_encode($rawData): $rawData;
            $computedSignature = $secret ? base64_encode(hash_hmac('sha256', $rawData, $secret, true)) : $receivedSignature;
            $flag = false;
            if ($receivedSignature === $computedSignature) {
                $this->logger->info('Webhook authenticity verification OK');
                $flag = true;
            } else {
                $this->logger->info('Webhook not authentic!');
            }
            //$this->logger->info($rawData);
            if ($flag) {
                // @todo Process the $requestData as needed
                $requestData = json_decode($rawData);
                $changedData = isset($requestData["mautic.lead_channel_subscription_changed"]) ? $requestData["mautic.lead_channel_subscription_changed"] : [];
                //$oldStatus = isset($changedData['old_status']) ? $changedData['old_status']: "";//unsubscribed, contactable
                $newStatus = isset($changedData['new_status']) ? $changedData['new_status']: "";//contactable, unsubscribed
                $message = __("OK");
                if (isset($changedData["contact"]) && isset($changedData["contact"]["fields"]) && isset($changedData["contact"]["fields"]["core"])) {
                    $coreFields = $changedData["contact"]["fields"]["core"];
                    $email = isset($coreFields["email"]) ? $coreFields["email"]: "";
                    $subscriber =  $this->_subscriberFactory->create()->loadByEmail($email);

                    switch ($newStatus) {
                        case "unsubscribed":
                            if ($subscriber->getId()) {
                                $subscriber->unsubscribe();
                                $message = __('OK, You unsubscribed.');
                            }
                            break;
                        case "contactable";
                            if ($subscriber->getId()) {
                                $subscriber->setStatus(Subscriber::STATUS_SUBSCRIBED)
                                    ->setStatusChanged(true)
                                    ->save();

                                //$subscriber->sendConfirmationSuccessEmail();
                                $message = __('OK, Your subscription has been confirmed.');
                            }
                            break;
                        default:
                            break;
                    }

                }
                // Process update subscriber status
                $this->logger->info("Mautic Webhook Processed successfully.");
                echo $message;
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
