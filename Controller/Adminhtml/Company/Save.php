<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Controller\Adminhtml\Company;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('company_id');

            $model = $this->_objectManager->create(\Lof\Mautic\Model\Company::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Company no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            if ($model->getId() && isset($data["mautic_company_id"]) && $model->getMauticCompanyId()) {
                $data["mautic_company_id"] = $model->getMauticCompanyId();
            }

            $this->_eventManager->dispatch('adminhtml_mautic_company_save_before', ['data' => $data, 'object' => $this, 'company' => $model]);

            $model->setData($data);

            try {
                $model->save();

                $this->_eventManager->dispatch('adminhtml_mautic_company_save_after', ['data' => $data, 'object' => $this, 'company' => $model]);

                $this->messageManager->addSuccessMessage(__('You saved the Company.'));
                $this->dataPersistor->clear('lof_mautic_company');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['company_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Company.'));
            }

            $this->dataPersistor->set('lof_mautic_company', $data);
            return $resultRedirect->setPath('*/*/edit', ['company_id' => $this->getRequest()->getParam('company_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

