<?php declare(strict_types=1);

namespace Lof\Mautic\Queue\MessageQueues\Customer;

use Lof\Mautic\Queue\MessageQueues\AbstractPublisher;

class Publisher extends AbstractPublisher
{
    /**
     * {@inheritdoc}
     */
    protected $_topic_name = 'mautic.magento.customer.save';
}
