<?php

namespace Lof\Mautic\Queue\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Helper\Data;
use Lof\Mautic\Model\Mautic\AbstractApi;
use Lof\Mautic\Queue\MessageQueues\Order\Publisher;

/**
 * Class ExportOrdersProcessor
 */
class ExportOrdersProcessor extends AbstractQueueProcessor
{
    /**
    * @var Publisher
    */
    private $publisher;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * CategoryImport constructor.
     *
     * @param Contact $mauticContact
     * @param Data $helperData
     * @param CollectionFactory $collectionFactory
     * @param Publisher $publisher
     */
    public function __construct(
        Contact $mauticContact,
        Data $helperData,
        CollectionFactory $collectionFactory,
        Publisher $publisher
    ) {
        parent::__construct($mauticContact, $helperData);
        $this->collectionFactory = $collectionFactory;
        $this->publisher = $publisher;
    }

    /**
     * @return void
     */
    public function process()
    {
        $collection = $this->collectionFactory->create()->addAttributeToSelect('*')
                                ->setOrder('created_at','desc');

        try {
            foreach ($collection as $order) {
                if ($this->helperData->isEnabled($order->getStoreId())) {
                    $attribution = (float)$order->getSubtotal();
                    $data = [
                        "email" => $order->getCustomerEmail(),
                        "firstname" => $order->getCustomerFirstname(),
                        "lastname" => $order->getCustomerLastname(),
                        "haspurchased" => true,
                        "attribution" => $attribution,
                        "tags" => "ordered"
                    ];
                    $address = $this->_getBillingAddress($order);
                    if ($address) {
                        $data = array_merge($data, $address);
                    }
                    if (!$this->helperData->isAyncApi($order->getStoreId())) {
                        $this->mauticContact->exportContact($data);
                    } else {
                        $this->publisher->execute(
                            $this->helperData->encodeData($data)
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            //log exception at here
        }
        return;

    }

    /**
     * Retrieve order billing address
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array|mixed|bool
     */
    protected function _getBillingAddress($order)
    {
        $address = $order->getBillingAddress();

        if ($address) {

            $country = $this->mauticContact->getCountryModel()->loadByCode($address->getCountryId());
            $street = $address->getStreet();
            return array(
                AbstractApi::MAUTIC_CUSTOMER_ADRESS1 => is_array($street) ? implode(", ", $street) : $street,
                AbstractApi::MAUTIC_CUSTOMER_ADRESS2 => "",
                AbstractApi::MAUTIC_CUSTOMER_ZIPCODE => $address->getPostcode(),
                AbstractApi::MAUTIC_CUSTOMER_COUNTRY => $country->getName(),
                AbstractApi::MAUTIC_CUSTOMER_STATE => $address->getRegion(),
                AbstractApi::MAUTIC_CUSTOMER_CITY => $address->getCity(),
                AbstractApi::MAUTIC_CUSTOMER_COMPANY => $address->getCompany(),
                AbstractApi::MAUTIC_CUSTOMER_PHONE => $address->getTelephone()
            );
        }
        return false;
    }
}
