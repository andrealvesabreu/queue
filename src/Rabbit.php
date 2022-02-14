<?php
declare(strict_types = 1);
namespace Inspire\Queue;

use Enqueue\AmqpExt\AmqpConnectionFactory;
use Interop\Amqp\AmqpTopic;
use Inspire\Support\Message\Serialize\MessageInterface;

/**
 * Description of Rabbit
 *
 * @author aalves
 */
class Rabbit extends BaseQueue implements QueueInterface

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
            $messageQueue = $this->context->createMessage($message->serialize());
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
     * @see QueueInterface::add()
     */
    public function addString(string $message): bool
    {
        try {
            $this->producer->send($this->topic, $this->context->createMessage($message));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Initialize a Producer
     *
     * {@inheritdoc}
     * @see \Inspire\Queue\QueueInterface::init()
     */
    public function init(array $config, string $queue): bool
    {
        try {
            /**
             * Connect to AMQP broker
             */
            $factory = new AmqpConnectionFactory($config);
            $this->context = $factory->createContext();
            $this->topic = $this->context->createTopic($config['exchange']);
            if (isset($config['persisted']) && $config['persisted']) {
                $this->topic->addFlag(AmqpTopic::FLAG_DURABLE);
            }
            switch ($config['queue_type']) {
                case 'direct':
                    $this->topic->setType(AmqpTopic::TYPE_DIRECT);
                    break;
                case 'fanout':
                    $this->topic->setType(AmqpTopic::TYPE_FANOUT);
                    break;
                case 'headers':
                    $this->topic->setType(AmqpTopic::TYPE_HEADERS);
                    break;
                case 'topic':
                    $this->topic->setType(AmqpTopic::TYPE_TOPIC);
                    break;
            }
            $this->queueName = $config['exchange'];
            $this->context->declareTopic($this->topic);
            $this->config = $config;
            $this->config['queue'] = $queue;
            $this->producer = $this->context->createProducer();
            return true;
        } catch (\Exception $e) {
            var_dump($e);
            return false;
        }
    }
}

