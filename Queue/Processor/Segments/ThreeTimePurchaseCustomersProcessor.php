<?php

namespace Lof\Mautic\Queue\Processor\Segments;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManager;
use Magento\Reports\Model\ResourceModel\Quote\CollectionFactory;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Helper\Data;
use Lof\Mautic\Model\Mautic\AbstractApi;
use Lof\Mautic\Queue\MessageQueues\Order\Publisher;
use Lof\Mautic\Queue\Processor\AbstractQueueProcessor;

/**
 * Class ThreeTimePurchaseCustomersProcessor
 * Find best customers from reports and add to segement name "Best Customers"
 */
class ThreeTimePurchaseCustomersProcessor extends AbstractQueueProcessor
{
    /**
     * @var array
     */
    protected $customergroups = [];

    /**
     * @var string|null
     */
    protected $firstdate = null;

    /**
    * @var Publisher
    */
    private $publisher;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StoreManager
     */
    protected $_storeManager;


    /**
     * CategoryImport constructor.
     *
     * @param Contact $mauticContact
     * @param Data $helperData
     * @param CollectionFactory $collectionFactory
     * @param Publisher $publisher
     * @param StoreManager $storeManager
     */
    public function __construct(
        Contact $mauticContact,
        Data $helperData,
        CollectionFactory $collectionFactory,
        Publisher $publisher,
        StoreManager $storeManager
    ) {
        parent::__construct($mauticContact, $helperData);
        $this->collectionFactory = $collectionFactory;
        $this->publisher = $publisher;
        $this->_storeManager    = $storeManager;
    }

    /**
     * @return void
     */
    public function process()
    {
        //Write code at here
    }
}
