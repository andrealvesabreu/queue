<?php
declare(strict_types = 1);
namespace Inspire\Queue;

use Inspire\Core\Message\MessageInterface;
use Enqueue\Redis\ {
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
    public function add(MessageInterface $message): bool
    {
        try {
            $messageQueue = $this->context->createMessage(serialize($message));
            $this->producer->send($this->topic, $messageQueue);
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
    public function init(array $config, string $queue): bool
    {
        try {
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
            $redis = new PhpRedis($cfg);
            $redis->connect();
            $factory = new RedisConnectionFactory($redis);
            $this->context = $factory->createContext();
            $this->config = $config;
            $this->config['queue'] = $queue;
            $this->topic = $this->context->createTopic($queue);
            $this->producer = $this->context->createProducer();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
