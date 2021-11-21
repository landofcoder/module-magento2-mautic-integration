<?php

declare(strict_types=1);

namespace Lof\Mautic\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\View\Element\Context;

class MauticFieldColumn extends Select
{
    /**
     * @var CollectionFactory
     */
    private $blockCollectionFactory;

    /**
     * @var Context
     */
    private $context;

    public function __construct(
        Context $context,
        CollectionFactory $blockCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blockCollectionFactory = $blockCollectionFactory;
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
            $this->_options = $this->blockCollectionFactory->create()->load()->toOptionArray();
            array_unshift($this->_options, ['value' => '', 'label' => __('Please select a static block.')]);
        }
        return $this->_options;
    }
}
