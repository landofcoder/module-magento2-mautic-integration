<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\Mautic\Override\Model;

use Magento\Framework\App\ObjectManager;
use Lof\Mautic\Helper\Data;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Queue\MessageQueues\Subscriber\Publisher;

/**
 * Subscriber model
 *
 */
class Subscriber extends \Magento\Newsletter\Model\Subscriber
{
    /**
    * @var Publisher|null
    */
    private $_publisher = null;

    /**
     * @var Lof\Mautic\Helper\Data|null
     */
    protected $_dataHelper = null;

    /**
     * @var Lof\Mautic\Model\Mautic\Contact|null
     */
    protected $_mauticModel = null;

    /**
     * @return Lof\Mautic\Helper\Data|null
     */
    protected function getDataHelper()
    {
        if (!$this->_dataHelper) {
            $this->_dataHelper = ObjectManager::getInstance()
            ->get(Data::class);
        }
        return $this->_dataHelper;
    }

    /**
     * @return Publisher
     */
    protected function getQueuePublisher()
    {
        if (!$this->_publisher) {
            $this->_publisher = ObjectManager::getInstance()
            ->get(Publisher::class);
        }
        return $this->_publisher;
    }

    /**
     * @return Lof\Mautic\Model\Mautic\Contact|null
     */
    protected function getMauticModel()
    {
        if (!$this->_mauticModel) {
            $this->_mauticModel = ObjectManager::getInstance()
            ->get(Contact::class);
        }
        return $this->_mauticModel;
    }

    /**
     * Sends out confirmation email
     *
     * @return $this
     */
    public function sendConfirmationRequestEmail()
    {
        if ($this->getDataHelper()->isEnabled() && $this->getDataHelper()->isDisabledNewsletter()) {
            $tags = ["confirm request"];
            $this->createMauticContact($tags);
            return $this;
        } else {
            return parent::sendConfirmationRequestEmail();
        }
    }

    /**
     * Sends out confirmation success email
     *
     * @return $this
     */
    public function sendConfirmationSuccessEmail()
    {
        $tags = ["subscribed"];
        $this->createMauticContact($tags);
        return parent::sendConfirmationSuccessEmail();
    }

    /**
     * Create Mautic contact for subscriber
     * @param array
     * @return mixed|object|null
     */
    public function createMauticContact($tags = [])
    {
        $mauticModel = $this->getMauticModel();
        if ($this->getDataHelper()->isEnabled()) {
            $tags[] = "newsletter";//subscribed
            $subscriberData = [
                "email" => $this->getEmail(),
                "firstname" => $this->getName(),
                "tags" => implode(",", $tags)
            ];
            if (!$this->getDataHelper()->isAyncApi()) {
                $mauticModel->exportContact($subscriberData);
            } else {
                $this->getQueuePublisher()->execute(
                    json_encode($subscriberData)
                );
            }
        }
        return $mauticModel;
    }

}
