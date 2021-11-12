<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Api\Data;

interface ContactSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Contact list.
     * @return \Lof\Mautic\Api\Data\ContactInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \Lof\Mautic\Api\Data\ContactInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

