<?php

declare(strict_types=1);

// Copyright (c) 2022 André Alves
// 
// This software is released under the MIT License.
// https://opensource.org/licenses/MIT

namespace Inspire\Queue;

use Inspire\Config\Config;

final class QueueFactory
{

    /**
     * Create a queue instance with $config configuration
     *
     * @param array $config
     * @param string $queueName
     * @return BaseQueue|NULL
     */
    public static function create(string $queueName, ?array $config = null): ?BaseQueue
    {
        if ($config === null) {
            $settings = Config::get("queue.{$queueName}");
            if ($settings === null) {
                throw new \Exception("Queue configuration not found");
            }
        } else {
            $settings = $config;
        }
        if ($settings == null) {
            throw new \Exception('Please set a driver configuration');
        }
        if (!isset($settings['driver'])) {
            throw new \Exception('Please set a driver configuration');
        }
        $classname = "\\Inspire\\Queue\\" . ucfirst($settings['driver']);
        if (!class_exists($classname)) {
            throw new \Exception('Driver is not supported');
        }
        $queue = new $classname();
        if ($queue->init($settings)) {
            return $queue;
        }
        throw new \Exception("Could not initialize queue. Check your configuration for '{$queueName}' queue.");
    }
}
