<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Mautic
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Mautic\Controller\Cart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Lof\Mautic\Queue\MessageQueues\Order\Publisher;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Helper\Data;

class Loadquote extends \Magento\Framework\App\Action\Action
{
    /**
    * @var Publisher|null
    */
    private $_publisher = null;

    /**
     * @var Lof\Mautic\Model\Mautic\Contact|null
     */
    protected $_mauticContact = null;

    /**
     * @var PageFactory
     */
    protected $pageFactory;
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quote;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Ebizmarts\MailChimp\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Magento\Framework\Url
     */
    protected $_urlHelper;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_message;
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Loadquote constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param \Magento\Quote\Model\QuoteFactory $quote
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Lof\Mautic\Helper\Data $helper
     * @param \Magento\Framework\Url $urlHelper
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param Publisher $publisher
     * @param Contact $mauticContact
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Lof\Mautic\Helper\Data $helper,
        \Magento\Framework\Url $urlHelper,
        \Magento\Customer\Model\Url $customerUrl,
        Publisher $publisher,
        Contact $mauticContact
    ) {
    
        $this->pageFactory      = $pageFactory;
        $this->_quote           = $quote;
        $this->_customerSession = $customerSession;
        $this->_helper          = $helper;
        $this->_urlHelper       = $urlHelper;
        $this->_message         = $context->getMessageManager();
        $this->_customerUrl     = $customerUrl;
        $this->_checkoutSession = $checkoutSession;
        $this->_publisher = $publisher;
        $this->_mauticContact = $mauticContact;

        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->pageFactory->create();
        $params     = $this->getRequest()->getParams();
        if (isset($params['id']) && $params['id']) {
            $quote = $this->_quote->create();
            $quote->getResource()->load($quote, $params['id']);
            $magentoStoreId = $quote->getStoreId();
            $configSecretKey = $this->_helper->getConfig(\Lof\Mautic\Helper\Data::MODULE_ABANDONEDCART_TOKEN);

            if (!isset($params['token']) || $params['token'] != $configSecretKey) {
                // @error
                $this->_message->addErrorMessage(__("You can't access this cart"));
                $url = $this->_urlHelper->getUrl(
                    $this->_helper->getConfig(
                        \Lof\Mautic\Helper\Data::MODULE_ABANDONEDCART_PAGE,
                        $magentoStoreId
                    )
                );
                $this->_redirect($url);
            } else {
                if (isset($params['mt_cid'])) {
                    $url = $this->_urlHelper->getUrl(
                        $this->_helper->getConfig(
                            \Lof\Mautic\Helper\Data::MODULE_ABANDONEDCART_PAGE,
                            $magentoStoreId
                        ),
                        ['mt_cid'=> $params['mt_cid']]
                    );
                    $quote->setData('mautic_campaign_id', $params['mt_cid']);
                } else {
                    $url = $this->_urlHelper->getUrl(
                        $this->_helper->getConfig(
                            \Lof\Mautic\Helper\Data::MODULE_ABANDONEDCART_PAGE,
                            $magentoStoreId
                        )
                    );
                }

                $quote->getResource()->save($quote);

                if ($emailAddress = $quote->getCustomerEmail()) {
                    $this->processUpdateContactTag($emailAddress, $quote->getStoreId());
                }
                
                if (!$quote->getCustomerId()) {
                    $this->_checkoutSession->setQuoteId($quote->getId());
                    $this->_redirect($url);
                } else {
                    if ($this->_customerSession->isLoggedIn()) {
                        $this->_redirect($url);
                    } else {
                        $this->_message->addNoticeMessage(__("Login to complete your order"));
                        if (isset($params['mt_cid'])) {
                            $url = $this->_urlHelper->getUrl(
                                $this->_customerUrl->getLoginUrl(),
                                ['mt_cid'=>$params['mt_cid']]
                            );
                        } else {
                            $url = $this->_customerUrl->getLoginUrl();
                        }
                        $this->_redirect($url);
                    }
                }
            }
        }
        return $resultPage;
    }

    /**
     * process update contact tags
     * 
     * @param string $email
     * @param int|string|null $storeId
     * @return bool
     */
    protected function processUpdateContactTag($email, $storeId = null)
    {
        if ($this->_helper->isEnabled($storeId)) {
            $removeTags = "-".(Data::ABANDONED_CART_TAGS);
            $data = [
                "email" => $email,
                "tags" => $removeTags
            ];
            if (!$this->_helper->isAyncApi($storeId)) {
                $this->_mauticContact->exportContact($data);
            } else {
                $this->_publisher->execute(
                    $this->_helper->encodeData($data)
                );
            }
        }
        return true;
    }
}