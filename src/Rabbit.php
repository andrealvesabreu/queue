<?php
declare(strict_types = 1);
namespace Inspire\Queue;

use Enqueue\AmqpExt\AmqpConnectionFactory;
use Interop\Amqp\AmqpTopic;
use Inspire\Core\Message\MessageInterface;

// use Interop\Amqp\AmqpQueue

// use \Interop\Amqp\Impl\AmqpBind;

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
            // $this->topic->setArguments([
            // 'alternate-exchange' => 'foo',
            // ]);
            $this->queueName = $config['exchange'];
            $this->context->declareTopic($this->topic);
            $this->config = $config;
            $this->config['queue'] = $queue;
            // $queue = $context->createQueue('track');
            // if (isset($config['persisted']) && $config['persisted']) {
            // $queue->addFlag(AmqpQueue::FLAG_DURABLE);
            // }
            // $context->declareQueue($queue);
            // $context->bind(new AmqpBind($topic, $queue));
            $this->producer = $this->context->createProducer();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

