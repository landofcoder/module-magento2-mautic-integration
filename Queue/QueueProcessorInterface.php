<?php declare(strict_types=1);

namespace Lof\Mautic\Queue;

/**
 * Interface QueueProcessorInterface
 */
interface QueueProcessorInterface
{
    /**
     * @return void
     */
    public function process();
}
