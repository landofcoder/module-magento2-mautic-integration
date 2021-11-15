<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Model;

use Lof\Mautic\Api\VisitorRepositoryInterface;
use Lof\Mautic\Api\Data\VisitorInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class VisitorRepository implements VisitorRepositoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function getVisitor()
    {
        //TODO: write code at here get current logged in customer + contact data info
    }
}

