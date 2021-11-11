<?php

namespace Lof\Mautic\Model\Adminhtml\Source;

class Button extends \Magento\Config\Block\System\Config\Form\Field
{
    public $config;
    protected $_template = 'Lof_Mautic::config/button.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Lof\Mautic\Helper\Data $config,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        $this->storeManager = $context->getStoreManager();
        $this->config = $config;
        $this->request = $request;
        $this->storeId = $this->getAdminConfigStoreId();
        parent::__construct($context, $data);
    }

    public function getAdminConfigStoreId()
    {
        $storeId = (int)$this->request->getParam('store', 0);
        $websiteId = (int)$this->request->getParam('website', 0);

        if ($storeId)
            return $storeId;
        else if ($websiteId)
            return $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId();

        return 0; // Default store
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getAjaxCheckUrl()
    {
        return $this->getUrl('mautic/contact/export');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'mautic_contact_export',
                'label' => __('Export'),
            ]
        );

        return $button->toHtml();
    }
}
