<?php declare(strict_types=1);

namespace Lof\Mautic\Queue\MessageQueues\Customer;

use Lof\Mautic\Queue\MessageQueues\AbstractPublisher;

class Publisher extends AbstractPublisher
{
    const TOPIC_NAME = 'mautic.magento.customer.save';
}
