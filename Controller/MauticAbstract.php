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
namespace Lof\Mautic\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;

abstract class MauticAbstract extends Action implements CsrfAwareActionInterface
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;


    /**
     * @var \Lof\Mautic\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\Mautic\Helper\Data $helperData,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_helperData = $helperData;
        $this->_customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Get Helper/Data
     *
     * @return \Lof\Mautic\Helper\Data
     */
    protected function getHelperData()
    {
        return $this->_helperData;
    }

    /**
     * Get CustomerSession
     *
     * @return void
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }


	/**
	 * Create Csrf validation exception
	 *
	 * @param  mixed $request
	 * @return InvalidRequestException
	 */
	public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

}
