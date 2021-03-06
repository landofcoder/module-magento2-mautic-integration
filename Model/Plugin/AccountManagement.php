<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\Mautic\Model\Plugin;

class AccountManagement
{
    /**
     * @var \Lof\Mautic\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quote;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * AccountManagement constructor.
     * @param \Lof\Mautic\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\QuoteFactory $quote
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Lof\Mautic\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
    
        $this->_helper  = $helper;
        $this->_quote   = $quote;
        $this->_session = $checkoutSession;
        $this->_storeManager = $storeManager;
    }

    /**
     * aroundIsEmailAvailable
     * 
     * @param \Magento\Customer\Model\AccountManagement $accountManagement
     * @param \Closure $proceed
     * @param $customerEmail
     * @param null $websiteId
     */
    public function aroundIsEmailAvailable(
        \Magento\Customer\Model\AccountManagement $accountManagement,
        \Closure $proceed,
        $customerEmail,
        $websiteId = null
    ) {
        $ret = $proceed($customerEmail, $websiteId);
        if ($this->_session && $this->_helper->isEnabled($this->_storeManager->getStore()->getId())) {
            $quoteId = $this->_session->getQuoteId();
            if ($quoteId) {
                $quote = $this->_quote->create();
                $quote->getResource()->load($quote, $quoteId);
                $quote->setCustomerEmail($customerEmail);
                $quote->setUpdatedAt(date('Y-m-d H:i:s'));
                $quote->getResource()->save($quote);
            }
        }
        return $ret;
    }
}
