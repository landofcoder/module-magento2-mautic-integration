<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Model;

use Lof\Mautic\Api\CompanyRepositoryInterface;
use Lof\Mautic\Api\Data\CompanyInterfaceFactory;
use Lof\Mautic\Api\Data\CompanySearchResultsInterfaceFactory;
use Lof\Mautic\Model\ResourceModel\Company as ResourceCompany;
use Lof\Mautic\Model\ResourceModel\Company\CollectionFactory as CompanyCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class CompanyRepository implements CompanyRepositoryInterface
{

    protected $dataObjectHelper;

    protected $extensionAttributesJoinProcessor;

    protected $extensibleDataObjectConverter;
    protected $dataCompanyFactory;

    protected $resource;

    protected $companyFactory;

    protected $dataObjectProcessor;

    private $collectionProcessor;

    private $storeManager;

    protected $searchResultsFactory;

    protected $companyCollectionFactory;


    /**
     * @param ResourceCompany $resource
     * @param CompanyFactory $companyFactory
     * @param CompanyInterfaceFactory $dataCompanyFactory
     * @param CompanyCollectionFactory $companyCollectionFactory
     * @param CompanySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCompany $resource,
        CompanyFactory $companyFactory,
        CompanyInterfaceFactory $dataCompanyFactory,
        CompanyCollectionFactory $companyCollectionFactory,
        CompanySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->companyFactory = $companyFactory;
        $this->companyCollectionFactory = $companyCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCompanyFactory = $dataCompanyFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lof\Mautic\Api\Data\CompanyInterface $company
    ) {
        /* if (empty($company->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $company->setStoreId($storeId);
        } */
        
        $companyData = $this->extensibleDataObjectConverter->toNestedArray(
            $company,
            [],
            \Lof\Mautic\Api\Data\CompanyInterface::class
        );
        
        $companyModel = $this->companyFactory->create()->setData($companyData);
        
        try {
            $this->resource->save($companyModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the company: %1',
                $exception->getMessage()
            ));
        }
        return $companyModel;
    }

    /**
     * {@inheritdoc}
     */
    public function get($companyId)
    {
        $company = $this->companyFactory->create();
        $this->resource->load($company, $companyId);
        if (!$company->getId()) {
            throw new NoSuchEntityException(__('Company with id "%1" does not exist.', $companyId));
        }
        return $company;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->companyCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lof\Mautic\Api\Data\CompanyInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lof\Mautic\Api\Data\CompanyInterface $company
    ) {
        try {
            $companyModel = $this->companyFactory->create();
            $this->resource->load($companyModel, $company->getCompanyId());
            $this->resource->delete($companyModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Company: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($companyId)
    {
        return $this->delete($this->get($companyId));
    }
}

