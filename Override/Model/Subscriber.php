<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\Mautic\Override\Model;

use Magento\Framework\App\ObjectManager;
use Lof\Mautic\Helper\Data;
use Lof\Mautic\Model\Mautic\Contact;

/**
 * Subscriber model
 *
 */
class Subscriber extends \Magento\Newsletter\Model\Subscriber
{

    protected $_dataHelper = null;

    protected $_mauticModel = null;

    protected function getDataHelper()
    {
        if (!$this->_dataHelper) {
            $this->_dataHelper = ObjectManager::getInstance()
            ->get(Data::class);
        }
        return $this->_dataHelper;
    }

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
        $mauticModel = $this->mauticContact;
        if ($this->getDataHelper()->isEnabled()) {
            $subscriberData = [
                "email" => $this->getEmail(),
                "firstname" => $this->getName(),
                "tags" => "newsletter,subscribed"
            ];
            $mauticModel->exportContact($subscriberData);
        }
        if ($this->getDataHelper()->isEnabled() && $this->getDataHelper()->isDisabledNewsletter()) {

            //send email to contact
            // email Id
            // contact ID
            $emailId = $this->getDataHelper()->getNewsletterEmailId();
            $params = [];
            $contactId = $mauticModel->getResponseContactId();
            if ($contactId) {
                $this->mauticContact->sendEmailToContact($emailId, $contactId, $params);
            }
            return $this;
        } else {
            return parent::sendConfirmationSuccessEmail();
        }
    }

    /**
     * Sends out unsubscription email
     *
     * @return $this
     */
    public function sendUnsubscriptionEmail()
    {
        if ($this->getDataHelper()->isEnabled() && $this->getDataHelper()->isDisabledNewsletter()) {
            return $this;
        } else {
            return parent::sendUnsubscriptionEmail();
        }
    }

}
