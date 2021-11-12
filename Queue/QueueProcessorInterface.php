<?php

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
