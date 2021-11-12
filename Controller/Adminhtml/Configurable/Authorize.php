<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Mautic
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/license-1-0
 */

namespace Lof\Mautic\Controller\Adminhtml\Configurable;

class Authorize extends \Lof\Mautic\Controller\Adminhtml\Configurable
{
    /**
     * @var \Lof\Mautic\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Lof\Mautic\Model\Mautic
     */
    protected $webhookSetup;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $dbTransaction;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Lof\Mautic\Helper\Data $helper
     * @param \Lof\Mautic\Model\Mautic $webhookSetup
     * @param \Magento\Framework\DB\Transaction $dbTransaction
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Lof\Mautic\Helper\Data $helper,
        \Lof\Mautic\Model\Mautic $webhookSetup,
        \Magento\Framework\DB\Transaction $dbTransaction,
        \Magento\Framework\Registry $coreRegistry
    )
    {

        parent::__construct($context, $coreRegistry);

        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->webhookSetup = $webhookSetup;
        $this->dbTransaction = $dbTransaction;
    }

    public function execute()
    {
        $resultAuthorize = $this->webhookSetup->authorize();
        $result = $this->resultJsonFactory->create();
        $countErrors = 0;
        $errorMessage = "";
        $success = false;

        if ($resultAuthorize===true || isset($resultAuthorize['access_token'])) {
            $success = true;
        }

        if (isset($resultAuthorize['errors'])) {
            $countErrors = count($resultAuthorize['errors']);
            $errorMessage = $resultAuthorize['errors'];
            $this->webhookSetup->executeErrorResponse($errorMessage);
        }
        return $result->setData(['success' => $success, 'errors' => $countErrors, 'errorMessage' => $errorMessage]);
    }
}
