<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Api;

interface VisitorRepositoryInterface
{
    /**
     * Retrieve Visitor - current logged in customer
     * @return \Lof\Mautic\Api\Data\VisitorInterface|boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getVisitor();

}
