<?php
declare(strict_types = 1);
namespace Inspire\Queue;

use Inspire\Core\Message\MessageInterface;

/**
 * Description of QueueInterface
 *
 * @author aalves
 */
interface QueueInterface
{

    /**
     * Initialize objects to connect a broker
     *
     * @param array $config
     * @param string $queue
     * @return bool
     */
    public function init(array $config, string $queue): bool;

    /**
     * Insert a message to current queue
     *
     * @param MessageInterface $message
     * @return bool
     */
    public function add(MessageInterface $message): bool;

    /**
     * COnsume and proccess messages from corrent queue
     */
    public function consume();
}

