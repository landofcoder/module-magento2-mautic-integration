<?php

namespace Lof\Mautic\Observer;

use Magento\Framework\Event\ObserverInterface;
use Lof\Mautic\Queue\MessageQueues\Customer\Publisher;

class CompanySaveAfter implements ObserverInterface
{
    /**
    * @var Publisher
    */
    private $_publisher;

    /**
     * @var \Lof\Mautic\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\Mautic\Model\Mautic\Company
     */
    protected $companyContact;

    /**
     * Construct customer save after observer
     *
     * @param \Lof\Mautic\Helper\Data $helper
     * @param \Lof\Mautic\Model\Mautic\Company $companyContact
     * @param Publisher $publisher
     */
    public function __construct(
        \Lof\Mautic\Helper\Data $helper,
        \Lof\Mautic\Model\Mautic\Company $companyContact,
        Publisher $publisher
    )
    {
        $this->helper = $helper;
        $this->companyContact = $companyContact;
        $this->publisher = $publisher;
    }

    /**
     * Sync company data to mautic
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) return $this;

        $companyModel = $observer->getCompany();
        if ($companyModel->getCompanyId() && $this->helper->isCompanyIntegrationEnabled()) {
            $this->companyContact->exportCompany($companyModel);
        }
        return $this;
    }
}
