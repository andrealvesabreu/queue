<?php

declare(strict_types=1);

// Copyright (c) 2022 André Alves
// 
// This software is released under the MIT License.
// https://opensource.org/licenses/MIT

namespace Inspire\Queue;

/**
 * @method static bool add(MessageInterface $message, ?array $properties = [], ?array $headers = [])
 * @method static bool addString(string $message, ?array $properties = [], ?array $headers = [])
 */
abstract class BaseQueue
{

    /**
     * Producer object
     *
     * @var \Interop\Queue\Producer
     */
    protected $producer = null;

    /**
     * Topic object
     *
     * @var \Interop\Queue\Topic
     */
    protected $topic = null;

    /**
     * Context object
     *
     * @var \Interop\Queue\Context
     */
    protected $context = null;

    /**
     * Queue configuration
     *
     * @var array
     */
    protected $config = null;

    /**
     * The of queue
     * 
     * @var string|null|null
     */
    protected ?string $queueName = null;

    /**
     * Consume queue
     *
     * @param Callable $processor
     */
    public function consume($processor = null): void
    {
        /**
         * Get queue from context
         *
         * @var \Interop\Queue\Queue $queue
         */
        $queue = $this->context->createQueue($this->config['queue']);
        /**
         * Create a consumer
         *
         * @var \Interop\Queue\Consumer $consumer
         */
        $consumer = $this->context->createConsumer($queue);
        /**
         * Get the processor function
         *
         * @var boolean $callable
         */
        $callable = false;
        if ($processor instanceof \Closure) {
            $callable = $processor;
        } else if (isset($this->config['consumer'])) {
            /**
             * If consumer is an array with class and method
             */
            if (is_array($this->config['consumer'])) {
                if (count($this->config['consumer']) == 2 && is_callable($this->config['consumer'])) {
                    $callable = $this->config['consumer'];
                } else if (count($this->config['consumer']) == 1 && method_exists($this->config['consumer'], 'fire')) {
                    $callable = [
                        $this->config['consumer'],
                        'fire'
                    ];
                }
            } else if (class_exists($this->config['consumer']) && method_exists($this->config['consumer'], 'fire')) {
                /**
                 * Default Consumer method must be "fire"
                 */
                $callable = [
                    $this->config['consumer'],
                    'fire'
                ];
            }
        }
        /**
         * If $callable is a valid Callable, process messages with it
         */
        if ($callable) {
            while ($message = $consumer->receive()) {
                if (call_user_func($callable, $message)) {
                    $consumer->acknowledge($message);
                } else {
                    // $consumer->reject($message, true);
                    $consumer->reject($message);
                }
            }
        }
    }
}
