<?php
/**
 * Copyright © landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Model\Config\Source;

class Cmspage
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    private $_page;
    /**
     * @param \Magento\Cms\Model\Page $page
     */
    public function __construct(
        \Magento\Cms\Model\Page $page
    ) {
        $this->_page = $page;
    }
    public function toOptionArray()
    {
        $pages = $this->_page->getCollection()->addOrder('title', 'asc');
        return ['checkout/cart' => 'Shopping Cart (default page)'] + $pages->toOptionIdArray();
    }
}
