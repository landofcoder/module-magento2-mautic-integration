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

use Magento\Framework\Exception\LocalizedException;

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
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Lof\Mautic\Helper\Data $helper
     * @param \Lof\Mautic\Model\Mautic $webhookSetup
     * @param \Magento\Framework\DB\Transaction $dbTransaction
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Lof\Mautic\Helper\Data $helper,
        \Lof\Mautic\Model\Mautic $webhookSetup,
        \Magento\Framework\DB\Transaction $dbTransaction,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {

        parent::__construct($context, $coreRegistry);

        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->webhookSetup = $webhookSetup;
        $this->dbTransaction = $dbTransaction;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $version = $this->getRequest()->getParam('version');
        if ($version == "2") {
            try {
                $resultPage = $this->resultPageFactory->create();
                $resultAuthorize = $this->webhookSetup->authorize();
                $block = $resultPage->getLayout()->createBlock('Magento\Framework\View\Element\Template', 'mautic_auth_result');
                if ($block) {
                    $block->setTemplate('Lof_Mautic::authorize/result.phtml');
                    if ($resultAuthorize===true || isset($resultAuthorize['access_token'])) {
                        $block->setResult('success');
                    }

                    if (isset($resultAuthorize['errors'])) {
                        $block->setResult('error')
                            ->setAuthErrors($resultAuthorize['errors']);
                    }

                    echo $block->toHtml();
                    return;
                }
                if ($resultAuthorize) {
                    print_r($resultAuthorize);
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while authorize API.'));
            }

            echo __('Something went wrong while authorize API.');

            return;
        } else {
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
}
