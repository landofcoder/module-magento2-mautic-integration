<?php declare(strict_types=1);

namespace Lof\Mautic\Queue\MessageQueues;

use Magento\Framework\MessageQueue\PublisherInterface;

class AbstractPublisher
{
    /**
     * @var string
     */
    protected $_topic_name = 'mautic.magento.customer.save';

     /**
      * @var \Magento\Framework\MessageQueue\PublisherInterface
      */
     private $publisher;

     /**
     * Publisher constructor.
     * @param PublisherInterface $publisher
     */
     public function __construct(
        PublisherInterface $publisher
     )
     {
        $this->publisher = $publisher;
     }

     /**
     * @param string $_data
     */
    public function execute(string $_data)
    {
        $this->publisher->publish($this->_topic_name, $_data);
    }
}
