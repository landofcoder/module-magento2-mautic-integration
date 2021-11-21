<?php

declare(strict_types=1);

namespace Lof\Mautic\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;
use Lof\Mautic\Helper\MappingField;

class MagentoCustomerFieldColumn extends Select
{
    /**
     * @var MappingField
     */
    private $mappingFieldHelper;

    /**
     * @var Context
     */
    private $context;

    public function __construct(
        Context $context,
        MappingField $mappingFieldHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->mappingFieldHelper = $mappingFieldHelper;
        $this->context = $context;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    /**
     * @return array
     */
    private function getSourceOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->mappingFieldHelper->getCustomerCustomFieldsArray();
        }
        return $this->_options;
    }

}
