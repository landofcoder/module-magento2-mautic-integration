<?php

namespace Lof\Mautic\Queue\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManager;
use Magento\Reports\Model\ResourceModel\Quote\CollectionFactory;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Helper\Data;
use Lof\Mautic\Model\Mautic\AbstractApi;
use Lof\Mautic\Queue\MessageQueues\Order\Publisher;

/**
 * Class AbandonedCartProcessor
 */
class AbandonedCartProcessor extends AbstractQueueProcessor
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
        $allStores = $this->_storeManager->getStores();
        foreach ($allStores as $storeId => $val) {
            $this->_storeManager->setCurrentStore($storeId);
            if ($this->helperData->isEnabled($storeId)) {
                $this->_processAbandoned($storeId);
            }
        }
        
        return;
    }

    /**
     * Process abandoned cart
     * 
     * @param int $storeId
     * @return void
     */
    protected function _processAbandoned($storeId)
    {
        $this->firstdate        = $this->helperData->getConfig(Data::MODULE_FIRST_DATE, $storeId);
        $this->customergroups        = $this->helperData->getConfig(Data::MODULE_ABANDONED_CUSTOMER_GROUP, $storeId);
        $token        = $this->helperData->getConfig(Data::MODULE_ABANDONEDCART_TOKEN, $storeId);

        if ($this->customergroups) {
            $this->customergroups = explode(",", $this->customergroups);
        } else {
            $this->customergroups = [];
        }
        $diff = $this->helperData->getConfig(Data::MODULE_DIFF_DATE, $storeId);
        $expr = sprintf('DATE_SUB(now(), %s)', $this->_getIntervalUnitSql($diff, 'DAY'));
        $from = new \Zend_Db_Expr($expr);

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('items_count', array('neq' => '0'))
            ->addFieldToFilter('main_table.is_active', '1')
            ->addFieldToFilter('main_table.store_id', array('eq' => $storeId))
            ->addSubtotal($storeId)
            ->setOrder('updated_at');

        $collection->addFieldToFilter('main_table.converted_at', array(array('null' => true), $this->_getSuggestedZeroDate()))
            ->addFieldToFilter('main_table.updated_at', array('to' => $from, 'from' => $this->firstdate));

        $collection->addFieldToFilter('main_table.customer_email', array('neq' => ''));
        if (count($this->customergroups)) {
            $collection->addFieldToFilter('main_table.customer_group_id', array('in' => $this->customergroups));
        }

        try {
            foreach ($collection as $quote) {
                $tokenNew = $token;//.md5(rand(0, 9999999));
                $url = $this->_storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK) . 'mautic/cart/loadquote?id=' . $quote->getEntityId() . '&token=' . $tokenNew;

                $customData = [
                    "email" => $quote->getCustomerEmail(),
                    "firstname" => $quote->getFirstname(),
                    "lastname" => $quote->getLastname(),
                    "tags" => Data::ABANDONED_CART_TAGS,
                    "cart_url" => $url
                ];
                $customer = $this->helperData->getCustomerById($quote->getCustomerId());
                if (!$this->helperData->isAyncApi()) {
                    $this->mauticContact->exportCustomer($customer, $customData);
                } else {
                    $data = $this->mauticContact->getRequestData($customData, $customer);
                    $this->publisher->execute(
                        $this->helperData->encodeData($data)
                    );
                }
            }
        } catch (\Exception $e) {
            //log exception at here
        }
        
        return;
    }

    /**
     * @param $interval
     * @param $this->unit
     * @return string
     */
    protected function _getIntervalUnitSql($interval, $unit)
    {
        return sprintf('INTERVAL %d %s', $interval, $unit);
    }

    /**
     * @return string
     */
    protected function _getSuggestedZeroDate()
    {
        return '0000-00-00 00:00:00';
    }
}
