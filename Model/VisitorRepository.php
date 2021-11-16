<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Model;

use Lof\Mautic\Api\VisitorRepositoryInterface;
use Lof\Mautic\Api\Data\VisitorInterfaceFactory;
use Lof\Mautic\Api\Data\VisitorInterface;
use Lof\Mautic\Helper\Data;
use Lof\Mautic\Model\ContactFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;

class VisitorRepository implements VisitorRepositoryInterface
{

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var ContactFactory
     */
    protected $contactFactory;

    /**
     * @var VisitorInterfaceFactory
     */
    protected $dataVisitorFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Construct VisitorRepository
     *
     * @param Data $helperData
     * @param CustomerFactory $customerFactory
     * @param CustomerSession $customerSession
     * @param ContactFactory $contactFactory
     * @param VisitorInterfaceFactory $dataVisitorFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        Data $helperData,
        CustomerFactory $customerFactory,
        CustomerSession $customerSession,
        ContactFactory $contactFactory,
        VisitorInterfaceFactory $dataVisitorFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->helperData = $helperData;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->contactFactory = $contactFactory;
        $this->dataVisitorFactory = $dataVisitorFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }
    /**
     * {@inheritdoc}
     */
    public function getVisitor()
    {
        //TODO: write code at here get current logged in customer + contact data info
        $customerId = $this->customerSession->getCustomerId();
        if ($customerId) {
            $customer = $this->customerSession->getCustomer();
            $contactItem = $this->contactFactory->create()->getCollection()
                                            ->addFieldToFilter("customer_id", $customerId)
                                            ->getFirstItem();
            $data = [
                "email" => $customer->getEmail(),
                "firstname" => $customer->getFirstname(),
                "lastname" => $customer->getLastname()
            ];
            if ($contactItem && $contactItem->getData("utm_campaign")) {
                $data["utm_campaign"] = $contactItem->getData("utm_campaign");
            }
            if ($contactItem && $contactItem->getData("utm_medium")) {
                $data["utm_medium"] = $contactItem->getData("utm_medium");
            }
            if ($contactItem && $contactItem->getData("company")) {
                $data["company"] = $contactItem->getData("company");
            }
            if ($contactItem && $contactItem->getData("companyemail")) {
                $data["companyemail"] = $contactItem->getData("companyemail");
            }

            return $this->getDataModel($data);
        }
        return false;
    }

    /**
     * Retrieve visitor model with visitor data
     * @param array $data
     * @return VisitorInterface
     */
    public function getDataModel(array $data)
    {
        $visitorDataObject = $this->dataVisitorFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $visitorDataObject,
            $data,
            VisitorInterface::class
        );

        return $visitorDataObject;
    }
}

