<?php
/**
 * LandofCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandofCoder
 * @package    Lof_Mautic
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Mautic\Controller\Adminhtml\Company;

use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Lof\Mautic\Controller\Adminhtml\Company
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Lof\Mautic\Model\ResourceModel\Company\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context                             $context           
     * @param \Magento\Ui\Component\MassAction\Filter                         $filter            
     * @param \Lof\Mautic\Model\ResourceModel\Company\CollectionFactory    $collectionFactory 
     * @param  \Magento\Framework\Registry                                    $coreRegistry      
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Lof\Mautic\Model\ResourceModel\Company\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context, $coreRegistry);
        $this->filter             = $filter;
        $this->collectionFactory  = $collectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $data = $this->getRequest()->getParams();
        if (isset($data['selected'])) {
            $collection = $this->collectionFactory->create()->addFieldToFilter('company_id', ['in' => $data['selected']]);
        }
        foreach ($collection as $rule) {
            $rule->delete();
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collection->count()));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Mautic::company_delete');
    }
}
