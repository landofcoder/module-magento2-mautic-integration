<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CompanyRepositoryInterface
{

    /**
     * Save Company
     * @param \Lof\Mautic\Api\Data\CompanyInterface $company
     * @return \Lof\Mautic\Api\Data\CompanyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\Mautic\Api\Data\CompanyInterface $company
    );

    /**
     * Retrieve Company
     * @param string $companyId
     * @return \Lof\Mautic\Api\Data\CompanyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($companyId);

    /**
     * Retrieve Company matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Mautic\Api\Data\CompanySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Company
     * @param \Lof\Mautic\Api\Data\CompanyInterface $company
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\Mautic\Api\Data\CompanyInterface $company
    );

    /**
     * Delete Company by ID
     * @param string $companyId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($companyId);
}

