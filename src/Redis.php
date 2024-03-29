<?php

declare(strict_types=1);

namespace Inspire\Queue;

use Inspire\Support\Message\Serialize\MessageInterface;
use Enqueue\Redis\{
    RedisConnectionFactory,
    PhpRedis
};

/**
 * Description of Redis
 *
 * @author aalves
 */
class Redis extends BaseQueue implements QueueInterface
{

    /**
     * Add a message to queue
     *
     * {@inheritdoc}
     * @see QueueInterface::add()
     */
    public function add(MessageInterface $message, ?array $properties = [], ?array $headers = []): bool
    {
        try {
            $messageQueue = $this->context->createMessage($message->serialize(), $properties ?? [], $headers ?? []);
            $this->producer->send($this->topic, $messageQueue);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Add a string message to queue
     *
     * {@inheritdoc}
     * @see QueueInterface::addString()
     */
    public function addString(string $message, ?array $properties = [], ?array $headers = []): bool
    {
        try {
            $this->producer->send($this->topic, $this->context->createMessage($message, $properties ?? [], $headers ?? []));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Initialize a Producer
     *
     * {@inheritdoc}
     * @see QueueInterface::init()
     */
    public function init(array $config): bool
    {
        try {
            if (!class_exists('\\Redis')) {
                throw new \Exception("Class Redis not found. Redis extension not installed or not enabled.");
            } else if (!class_exists('Enqueue\\Redis\\PhpRedis')) {
                throw new \Exception("Please install enqueue/redis package.");
            }
            /**
             * Connect to Redis Server
             */
            $cfg = [
                'host' => $config['host'],
                'port' => $config['port'],
                'scheme' => 'redis'
            ];
            if (isset($config['user'])) {
                $cfg['username'] = $config['user'];
            }
            if (isset($config['pass'])) {
                $cfg['password'] = $config['pass'];
            }
            $cfg['persistent'] = $config['persisted'] ?? false;
            $cfg['read_write_timeout'] = $config['read_timeout'] ?? 30;
            $cfg['timeout'] = $config['connection_timeout'] ?? 30;
            $cfg['database'] = $config['database'] ?? 1;
            /**
             * Initialize Redis connection
             */
            $redis = new PhpRedis($cfg);
            $redis->connect();
            $factory = new RedisConnectionFactory($redis);
            /**
             * Create context and producer
             */
            $this->context = $factory->createContext();
            $this->queueName = $config['name'];
            $this->config = $config;
            $this->topic = $this->context->createTopic($this->config['queue']);
            $this->producer = $this->context->createProducer();
            return true;
        } catch (\Exception $e) {
            throw  $e;
            // return false;
        }
    }
}
