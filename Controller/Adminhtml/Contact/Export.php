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

namespace Lof\Mautic\Controller\Adminhtml\Contact;

class Export extends \Lof\Mautic\Controller\Adminhtml\Contact
{
    /**
     * @var \Lof\Mautic\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\Mautic\Model\Mautic\Contact
     */
    protected $customerContact;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Lof\Mautic\Helper\Data $helper
     * @param \Lof\Mautic\Model\Mautic\Contact $customerContact
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Lof\Mautic\Helper\Data $helper,
        \Lof\Mautic\Model\Mautic\Contact $customerContact,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context, $coreRegistry);

        $this->helper = $helper;
        $this->customerContact = $customerContact;
        $this->resultJsonFactory = $resultJsonFactory;

    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $resultExport = array(
            'success' => true
        );

        try {
            $resultExport['success'] = $this->customerContact->export();
        } catch(\Exception $e) {
            $resultExport = array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
        return $result->setData($resultExport);
    }
}
