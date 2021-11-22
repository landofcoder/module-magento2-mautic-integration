<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\Mautic\Model\Plugin;

use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Queue\MessageQueues\Subscriber\Publisher;
use Lof\Mautic\Queue\MessageQueues\ContactDelete\Publisher as DeletePublisher;

class Subscriber
{
    /**
    * @var Publisher|null
    */
    private $_publisher = null;

    /**
    * @var DeletePublisher|null
    */
    private $_deletePublisher = null;

    /**
     * @var Lof\Mautic\Model\Mautic\Contact|null
     */
    protected $_mauticContact = null;

    /**
     * @var \Lof\Mautic\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Subscriber constructor.
     * @param \Lof\Mautic\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Publisher $publisher
     * @param Contact $mauticContact
     * @param DeletePublisher $deletePublisher
     */
    public function __construct(
        \Lof\Mautic\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Publisher $publisher,
        Contact $mauticContact,
        DeletePublisher $deletePublisher
    ) {
    
        $this->_helper          = $helper;
        $this->_storeManager    = $storeManager;
        $this->_publisher = $publisher;
        $this->_mauticContact = $mauticContact;
        $this->_deletePublisher = $deletePublisher;
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterDelete(
        \Magento\Newsletter\Model\Subscriber $subscriber
    ) {

        $storeId = $this->getStoreIdFromSubscriber($subscriber);
        if ($this->_helper->isEnabled($storeId)) {
            if ($subscriber->isSubscribed()) {
                try {
                    $email = $subscriber->getSubscriberEmail();
                    if (!$subscriber->getCustomerId()) {
                        $contacts = $this->_mauticContact->getList("email:$email", 0, 1, 'email', 'asc');
                        if ($contacts && isset($contacts["total"]) && (int)$contacts["total"] > 0 && isset($contacts["contacts"])) {
                            foreach ($contacts["contacts"] as $contactId => $contact) {
                                if (!$this->_helper->isAyncApi($storeId)) {
                                    $this->_mauticContact->deleteRecord((int)$contactId);
                                } else {
                                    $data = ["mautic_contact_id" => (int)$contactId];
                                    $this->_deletePublisher->execute(
                                        $this->_helper->encodeData($data)
                                    );
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    //
                }
            }
        }
        return null;
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @return int
     */
    protected function getStoreIdFromSubscriber(\Magento\Newsletter\Model\Subscriber $subscriber)
    {
        return $subscriber->getStoreId();
    }

    /**
     * afterSendConfirmationSuccessEmail
     * 
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @return \Magento\Newsletter\Model\Subscriber
     */
    public function afterSendConfirmationSuccessEmail(\Magento\Newsletter\Model\Subscriber $subscriber)
    {
        try {
            if ($this->_helper->isEnabled($subscriber->getStoreId())) {
                $tags = ["subscribed"];
                $this->createMauticContact($subscriber->getEmail(), $subscriber->getName(), $tags, $subscriber->getStoreId());
            }
        } catch (\Exception $exception) {
            //
        }

        return $subscriber;
    }

     /**
     * aroundSendConfirmationRequestEmail
     * 
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param \Closure $proceed
     */
    public function aroundSendConfirmationRequestEmail(
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Closure $proceed
    ) {
        if ($this->_helper->isEnabled($subscriber->getStoreId()) && $this->_helper->isDisabledNewsletter($subscriber->getStoreId())) {
            $tags = ["confirm request"];
            $this->createMauticContact($subscriber->getEmail(), $subscriber->getName(), $tags, $subscriber->getStoreId());
            return $subscriber;
        } else {
            return $proceed();
        }
    }

    /**
     * Create Mautic contact for subscriber
     * @param string $email
     * @param string $name
     * @param array $tags
     * @param int|mixed|null $storeId
     * @return mixed|object|null
     */
    public function createMauticContact($email, $name, $tags = [], $storeId = null)
    {
        if (!$email) {
            return null;
        }
        $mauticContact = $this->_mauticContact;
        if ($this->_helper->isEnabled($storeId)) {
            $tags[] = "newsletter";//subscribed
            $subscriberData = [
                "email" => $email,
                "firstname" => $name,
                "tags" => implode(",", $tags)
            ];
            if (!$this->_helper->isAyncApi($storeId)) {
                $mauticContact->exportContact($subscriberData);
            } else {
                $this->_publisher->execute(
                    json_encode($subscriberData)
                );
            }
        }
        return $mauticContact;
    }
}
