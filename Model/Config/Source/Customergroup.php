<?php
/**
 * Copyright © landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Model\Config\Source;

class Customergroup
{
    protected $_options;
    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $groupFactory;

    /**
     * @param \Magento\Customer\Model\GroupFactory $groupFactory
     */
    public function __construct(
        \Magento\Customer\Model\GroupFactory $groupFactory
    ) {
        $this->groupFactory = $groupFactory;
    }


    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->groupFactory->create()->getCollection()
                ->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}