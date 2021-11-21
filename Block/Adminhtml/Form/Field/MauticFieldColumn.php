<?php

declare(strict_types=1);

namespace Lof\Mautic\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\View\Element\Context;
use Lof\Mautic\Model\Mautic\Contact;

class MauticFieldColumn extends Select
{

    /**
     * @var Contact
     */
    private $mauticContact;

    /**
     * @var Context
     */
    private $context;

    public function __construct(
        Context $context,
        Contact $mauticContact,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->context = $context;
        $this->mauticContact = $mauticContact;
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

            $this->_options = $this->mauticContact->getContactCustomFieldsArray();

            array_unshift($this->_options, ['value' => '', 'label' => __('Please select a field.')]);
        }
        return $this->_options;
    }
}
