<?php declare(strict_types=1);

namespace Lof\Mautic\Model\Mautic;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Campaign extends AbstractApi
{
    /**
     * @var string
     */
    protected $_api_type = "campaigns";

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;


    /**
     * Initialize resource model
     *
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Lof\Mautic\Model\Mautic $mauticModel
    ) {
        parent::__construct($context, $registry, $customerFactory, $countryFactory, $mauticModel );
        $this->countryFactory = $countryFactory;
    }

    /**
     * Export contacts from customer
     *
     * @return bool
     */
    public function export()
    {
        return true;
    }

}
