<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Api\Data;

interface CompanySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Company list.
     * @return \Lof\Mautic\Api\Data\CompanyInterface[]
     */
    public function getItems();

    /**
     * Set companyname list.
     * @param \Lof\Mautic\Api\Data\CompanyInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

