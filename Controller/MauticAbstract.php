<?php
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
