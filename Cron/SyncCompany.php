<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Mautic
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Mautic\Cron;

use Lof\Mautic\Helper\Data;
use Lof\Mautic\Model\CompanyFactory;
use Lof\Mautic\Model\Mautic\Company as MauticCompany;

/**
 * Class SyncContact
 *
 * @package Lof\Mautic\Cron
 */
class SyncContact
{
    protected $helper;

    /**
     * @var CompanyFactory
     */
    private $_companyFactory;

    /**
     * @var MauticCompany
     */
    protected $_mauticCompany;

    public function __construct(
        Data $helper,
        CompanyFactory $companyFactory,
        MauticCompany $mauticCompany
    ) {
        $this->helper = $helper;
        $this->_companyFactory = $companyFactory;
        $this->_mauticCompany = $mauticCompany;
    }

    /**
     * Execute view action
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        ///
    }
}
