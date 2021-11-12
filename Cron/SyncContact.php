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
use Lof\Mautic\Model\ContactFactory;
use Lof\Mautic\Model\Mautic\Contact as MauticContact;

/**
 * Class SyncContact
 *
 * @package Lof\Mautic\Cron
 */
class SyncContact
{
    protected $helper;

    /**
     * @var ContactFactory
     */
    private $_contactFactory;

    /**
     * @var MauticContact
     */
    protected $_mauticContact;

    public function __construct(
        Data $helper,
        ContactFactory $contactFactory,
        MauticContact $mauticContact
    ) {
        $this->helper = $helper;
        $this->_contactFactory = $contactFactory;
        $this->_mauticContact = $mauticContact;
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
