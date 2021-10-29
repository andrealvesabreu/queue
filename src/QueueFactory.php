<?php
declare(strict_types = 1);
namespace Inspire\Queue;

/**
 * Description of QueueFactory
 *
 * @author aalves
 */
final class QueueFactory
{

    /**
     * Create a queue instance with $config configuration
     *
     * @param array $config
     * @param string $queueName
     * @return BaseQueue|NULL
     */
    public static function create(array $config, string $queueName): ?BaseQueue
    {
        if (! isset($config['driver'])) {
            throw new \Exception('Please set a driver configuration');
        }
        $classname = "\\Inspire\\Queue\\" . ucfirst($config['driver']);
        if (! class_exists($classname)) {
            throw new \Exception('Driver is not supported');
        }
        $queue = new $classname();
        if ($queue->init($config, $queueName)) {
            return $queue;
        }
        throw new \Exception("Could not initialize queue. Check your configuration for '{$queueName}' queue.");
    }
}

