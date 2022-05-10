<?php

declare(strict_types=1);

// Copyright (c) 2022 André Alves
// 
// This software is released under the MIT License.
// https://opensource.org/licenses/MIT

namespace Inspire\Queue;

use Inspire\Config\Config;

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
    public static function on(string $queueName): ?BaseQueue
    {
        if (!isset(self::$queues[$queueName])) {
            $first = empty(self::$queues);
            self::$queues[$queueName] = QueueFactory::create($queueName);
            /**
             * If this is first handler, allow to use it as default
             */
            if ($first) {
                self::$queues['default'] = self::$queues[$queueName];
            }
            return self::$queues[$queueName];
        } else {
            return self::$queues[$queueName];
        }
        throw new \Exception("Invalid queue configuration");
    }

    /**
     * Get a specific handler to file manipulation
     * 
     * @param string $handlerName
     * @param array $config
     * 
     * @return BaseFs|null
     */
    public static function with(string $queueName, array $config): ?BaseQueue
    {
        if (!isset(self::$queues[$queueName])) {
            $first = empty(self::$queues);
            self::$queues[$queueName] = QueueFactory::create($queueName, $config);
            /**
             * If this is first queue, allow to use it as default
             */
            if ($first) {
                self::$queues['default'] = &self::$queues[$queueName];
            }
            return self::$queues[$queueName];
        } else {
            return self::$queues[$queueName];
        }
        throw new \Exception("Invalid filesystem configuration");
    }

    /**
     * Call statically 
     */
    public static function __callStatic($name, $arguments)
    {
        /**
         * Use default queue if calling statically a non static method
         * If default queue does not exists, thrown an exception
         */
        if (!isset(self::$queues['default'])) {
            throw new \Exception("There is no one queue started.");
        }
        /**
         * Dispatch call through default queue
         */
        return call_user_func_array([self::$queues['default'], $name], $arguments);
    }
}
