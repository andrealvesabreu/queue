<?php
declare(strict_types = 1);
namespace Inspire\Queue;

use Inspire\Support\Config;

/**
 *
 * @author aalves
 *        
 */
final class Queue
{

    private static $queues = [];

    /**
     * Get a specific queue connection to add or consume messages
     *
     * @param string $queueName
     * @throws \Exception
     * @return mixed
     */
    public static function on(string $queueName)
    {
        if (! isset(self::$queues[$queueName])) {
            $config = Config::get("queue.{$queueName}");
            if ($config !== null) {
                $queue = QueueFactory::create($config, $queueName);
                self::$queues[$queueName] = $queue;
                return self::$queues[$queueName];
            }
        } else {
            return self::$queues[$queueName];
        }
        throw new \Exception("Invalid queue configuration");
    }
}
