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
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
declare(strict_types=1);

namespace Lof\Mautic\Helper;

use Lof\Mautic\Model\Mautic\AbstractApi;

class MappingField extends Data
{
    /**
     * @var array
     */
    protected $_options = [];

    /**
     * @return array
     */
    public function getCustomerCustomFieldsArray()
    {
        if (!$this->_options) {
            $customer_attributes = $this->customerFactory->create()->getAttributes();

            $attributesArrays = array();
            $attributesArrays[] = ['value' => 'customer_information', 'label' => __('CUSTOMER INFORMATION'), 'disabled' => 'disabled'];

            foreach($customer_attributes as $attribute_code => $attribute){
                $attributesArrays[] = array(
                    'label' => $attribute->getStoreLabel(),
                    'value' => $attribute_code
                );
            }

            $attributesArrays[] = ['value' => 'customer_address', 'label' => __('CUSTOMER ADDRESS'), 'disabled' => 'disabled'];
            $addressArrays = $this->getCustomerAddressArray();
            foreach ($addressArrays as $key => $label) {
                $attributesArrays[] = array(
                    'label' => $label,
                    'value' => $key
                );
            }
            $this->_options = $attributesArrays;
            array_unshift($this->_options, ['value' => '', 'label' => __('Please select a Field.')]);
        }
        return $this->_options;
    }

    /**
     * @return array
     */
    protected function getCustomerAddressArray()
    {
        return [
            'city' => __("City"),
            'company' => __("Company"),
            'country_id' => __("Country"),
            'fax' => __("Fax"),
            'firstname' => __("First Name"),
            'lastname' => __("Last Name"),
            'middlename' => __("Middle Name/Initial"),
            'postcode' => __("Zip/Postal Code"),
            'prefix' => __("Prefix"),
            'region' => __("State/Province"),
            'region_id' => __("State/Province"),
            'street' => __("Street Address"),
            'suffix' => __("Suffix"),
            'telephone' => __("Phone Number"),
            'vat_id' => __("VAT Number"),
            'vat_is_valid' => __("VAT number validity"),
            'vat_request_date' => __("VAT number validation request date"),
            'vat_request_id' => __("VAT number validation request ID"),
            'vat_request_success' => __("VAT number validation request success")
        ];
    }
}
