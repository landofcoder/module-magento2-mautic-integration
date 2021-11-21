<?php

declare(strict_types=1);

namespace Lof\Mautic\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;

class MappingOptions extends AbstractFieldArray
{
    /**
     * @var MauticFieldColumn
     */
    private $mauticFieldRenderer;

    /**
     * @var MagentoCustomerFieldColumn
     */
    private $customerFieldRenderer;

    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'mautic_field_id',
            [
                'label' => __('Mautic Contact Fields'),
                'renderer' => $this->getMauticFieldBlockRenderer()
            ]
        );
        $this->addColumn(
            'magento_customer_fields',
            [
                'label' => __('Magento Customer Fields'),
                'renderer' => $this->getCustomeFieldBlockRenderer()
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $mauticFieldId = $row->getMauticFieldId();
        if ($mauticFieldId !== null) {
            $options['option_' . $this->getMauticFieldBlockRenderer()->calcOptionHash($mauticFieldId)] = 'selected="selected"';
        }

        $customerFieldId = $row->getMagentoCustomerFields();
        if ($customerFieldId !== null) {
            $options['option_' . $this->getCustomeFieldBlockRenderer()->calcOptionHash($customerFieldId)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return BlockInterface|MauticFieldColumn
     * @throws LocalizedException
     */
    private function getMauticFieldBlockRenderer()
    {
        if (!$this->mauticFieldRenderer) {
            $this->mauticFieldRenderer = $this->getLayout()->createBlock(
                MauticFieldColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->mauticFieldRenderer;
    }

    /**
     * @return BlockInterface|MagentoCustomerFieldColumn
     * @throws LocalizedException
     */
    private function getCustomeFieldBlockRenderer()
    {
        if (!$this->customerFieldRenderer) {
            $this->customerFieldRenderer = $this->getLayout()->createBlock(
                MagentoCustomerFieldColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->customerFieldRenderer;
    }
}
