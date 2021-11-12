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

namespace Lof\Mautic\Controller\Adminhtml\Company;

class Sync extends \Lof\Mautic\Controller\Adminhtml\Company
{
    /**
     * @var \Lof\Mautic\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\Mautic\Model\Mautic\Company
     */
    protected $customerContact;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Lof\Mautic\Helper\Data $helper
     * @param \Lof\Mautic\Model\Mautic\Company $customerContact
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Lof\Mautic\Helper\Data $helper,
        \Lof\Mautic\Model\Mautic\Company $customerContact,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $coreRegistry);

        $this->helper = $helper;
        $this->customerContact = $customerContact;
        $this->resultPageFactory = $resultPageFactory;

    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Lof_Mautic::contacts')
            ->addBreadcrumb(__('Mautic'), __('Mautic'))
            ->addBreadcrumb(__('Companies'), __('Companies'));

        try {
            $response = $this->customerContact->getList();
            echo "<pre>";
            print_r($response);
            die();
        } catch(\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());
        }
        return $resultPage;
    }
}
