<?php declare(strict_types=1);

namespace Lof\Mautic\Queue\MessageQueues;

use Lof\Mautic\Queue\Processor\AbstractQueueProcessor;
use Lof\Mautic\Model\Mautic\Contact;
use Lof\Mautic\Helper\Data;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Consumer
 */
class Consumer extends AbstractQueueProcessor
{
    /**
     * @var SerializerInterface
     */
    private $serialize;

    /**
     * @var Contact
     */
    protected $mauticContact;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * CategoryImport constructor.
     *
     * @param Contact $mauticContact
     * @param Data $helperData
     * @param SerializerInterface $serialize
     */
    public function __construct(
        Contact $mauticContact,
        Data $helperData,
        SerializerInterface $serialize
    ) {
        parent::__construct($mauticContact, $helperData);
        $this->serialize = $serialize;
    }

    public function processMessage(string $_data)
    {
        // do something with message queue data
        if ($_data) {
            $data = $this->serialize->unserialize($_data);
            if ($data && ( (isset($data["email"]) && $data["email"]) || (isset($data["mautic_contact_id"]) && $data["mautic_contact_id"]))) {
                $this->mauticContact->exportContact($data);
            }
        }
    }
}
