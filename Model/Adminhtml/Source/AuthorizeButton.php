<?php

namespace Lof\Mautic\Model\Adminhtml\Source;

class AuthorizeButton extends \Magento\Config\Block\System\Config\Form\Field
{
    public $config;
    protected $_template = 'Lof_Mautic::config/authorize_button.phtml';
    protected $storedAccessTokenData = null;

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
        $key = $this->config->getClientSecret($this->storeId);
        $storedAccessTokenData = $this->config->getStoredAccessTokenData($this->storeId);
        $this->storedAccessTokenData = $storedAccessTokenData;
        if (empty($key) || $storedAccessTokenData)
            $this->_template = 'Lof_Mautic::config/authorize_button_disabled.phtml';

        parent::__construct($context, $data);
    }

    public function getStoredAccessTokenData()
    {
        return $this->storedAccessTokenData;
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

    public function getAuthorizeUrl()
    {
        return $this->getUrl('mautic/configurable/authorize');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'mautic_configure_webhooks',
                'label' => __('Authorize API'),
            ]
        );

        return $button->toHtml();
    }

    public function getDisabledButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'mautic_configure_webhooks',
                'label' => __('Authorize API'),
                'disabled' => true
            ]
        );

        return $button->toHtml();
    }
}
