<?php declare(strict_types=1);

namespace Lof\Mautic\Queue\MessageQueues\Order;

use Lof\Mautic\Queue\MessageQueues\AbstractPublisher;

class Publisher extends AbstractPublisher
{
    const TOPIC_NAME = 'mautic.magento.order.create';
}
