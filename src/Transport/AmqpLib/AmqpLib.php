<?php

declare(strict_types=1);

namespace Borodulin\Queue\Transport\AmqpLib;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class AmqpLib
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;
    /**
     * @var AMQPChannel
     */
    private $channel;
    private $exchanges;
    private $queues;

    public function __construct(
        $host = 'localhost',
        $port = 5672,
        $user = 'guest',
        $password = 'guest'
    ) {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->connection->channel();
    }

    public function getExchange($exchangeName)
    {
        if (!isset($this->exchanges[$exchangeName])) {
            $this->exchanges[$exchangeName] = $this->channel
                ->exchange_declare($exchangeName, 'direct', false, false, false);
        }
        return $this->exchanges[$exchangeName];
    }

    public function getQueue(string $queueName)
    {
        if (!isset($this->queues[$queueName])) {
            $this->getExchange($queueName);
            $this->queues[$queueName] = $this->channel
                ->queue_declare($queueName, false, true, false, false);
            $this->channel->queue_bind($queueName, $queueName, $queueName);
        }
        return $this->queues[$queueName];
    }

    public function sendJsonMessage(string $queueName, $message, array $options = [])
    {
        if (!is_string($message)) {
            $message = json_encode($message);
        }
        $options = array_replace([
            'delivery_mode' => AMQP_DURABLE,
            'content_type' => 'application/json',
        ], $options);
        $this->getQueue($queueName);

        $msg = new AMQPMessage($message, $options);
        $this->channel->basic_publish($msg, $queueName, $queueName);
    }

    /**
     * @return AMQPChannel
     */
    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }
}
