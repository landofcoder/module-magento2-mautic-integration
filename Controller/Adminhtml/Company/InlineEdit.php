<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Controller\Adminhtml\Company;

class InlineEdit extends \Magento\Backend\App\Action
{

    protected $jsonFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Inline edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);

            $this->_eventManager->dispatch('adminhtml_mautic_company_inline_edit_before', ['data' => $postItems, 'object' => $this]);

            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                $companies = [];
                foreach (array_keys($postItems) as $modelid) {
                    /** @var \Lof\Mautic\Model\Company $model */
                    $model = $this->_objectManager->create(\Lof\Mautic\Model\Company::class)->load($modelid);
                    try {
                        $model->setData(array_merge($model->getData(), $postItems[$modelid]));
                        $model->save();
                        $companies[] = $model;
                    } catch (\Exception $e) {
                        $messages[] = "[Company ID: {$modelid}]  {$e->getMessage()}";
                        $error = true;
                    }
                }

                $this->_eventManager->dispatch('adminhtml_mautic_company_inline_edit_after', ['data' => $postItems, 'object' => $this, 'companies' => $companies]);
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}

