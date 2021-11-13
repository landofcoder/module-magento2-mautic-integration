<?php

namespace Lof\Mautic\Model\Mautic;

/**
 * Class AbstractApi
 */
abstract class AbstractApi extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    public function processSyncFromMautic()
    {
        return;

    }

    /**
     * Get list objects
     *
     * @param string $filter
     * @param int $start
     * @param int $limit
     * @param string $orderBy
     * @param string $orderByDir
     * @return array|mixed|string|bool|null
     */
    public function getList($filter = "", $start = 0, $limit = 10, $orderBy = "", $orderByDir = "DESC")
    {
        return [];
    }

    /**
     * @return bool
     */
    public function export()
    {
        return;

    }

    /**
     * @param string|int $id
     * @return array|mixed|bool
     */
    public function getItemById($id = "")
    {
        return [];

    }

    /**
     * @param string|int $id
     * @param array|mixed
     * @return array|mixed|bool
     */
    public function updateRecord($id, $data = [])
    {
        return [];

    }

    /**
     * @param string|int $id
     *
     * @return bool
     */
    public function deleteRecord($id)
    {
        return true;

    }

    /**
     * get all companies
     * 
     * @return array|bool|null
     */
    public function getCompanies()
    {
        return [];
    }

    /**
     * get all tags
     * 
     * @return array|bool|null
     */
    public function getTags()
    {
        return [];
    }

    /**
     * get all stages
     * 
     * @return array|bool|null
     */
    public function getStages()
    {
        return [];
    }

    /**
     * get all company fields
     * 
     * @return array|bool|null
     */
    public function getCompanyFields()
    {
        return [];
    }

    /**
     * get all industries
     * 
     * @return array|bool|null
     */
    public function getIndustries()
    {
        return [];
    }

    /**
     * get all segments
     * 
     * @return array|bool|null
     */
    public function getSegments()
    {
        return [];
    }

    /**
     * get all campaigns
     * 
     * @return array|bool|null
     */
    public function getCampaigns()
    {
        return [];
    }
}
